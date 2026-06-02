<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\PedidoHistorial;
use App\Support\EstatusCatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminPedidoSeguimientoService
{
    public const ACCION_CONFIRMAR = 'confirmar';

    public const ACCION_PREPARACION = 'preparacion';

    public const ACCION_ENVIADO = 'enviado';

    public const ACCION_ENTREGADO = 'entregado';

    public function __construct(
        protected PedidoService $pedidoService,
        protected InventarioPedidoService $inventarioPedidoService
    ) {}

    /**
     * @return self::ACCION_*|null
     */
    public function accionDisponible(Pedido $pedido): ?string
    {
        $pedido->loadMissing(['pago']);

        $estatusId = (int) $pedido->Id_Estatus;

        if ($estatusId === EstatusCatalog::PEDIDO_CANCELADO) {
            return null;
        }

        return match ($estatusId) {
            EstatusCatalog::PEDIDO_PENDIENTE => self::ACCION_CONFIRMAR,
            EstatusCatalog::PEDIDO_CONFIRMADO => self::ACCION_PREPARACION,
            EstatusCatalog::PEDIDO_EN_PREPARACION => self::ACCION_ENVIADO,
            EstatusCatalog::PEDIDO_ENVIADO => self::ACCION_ENTREGADO,
            default => null,
        };
    }

    public function etiquetaAccion(?string $accion): string
    {
        return match ($accion) {
            self::ACCION_CONFIRMAR => 'Confirmar pedido',
            self::ACCION_PREPARACION => 'Marcar en preparación',
            self::ACCION_ENVIADO => 'Marcar como enviado',
            self::ACCION_ENTREGADO => 'Marcar como entregado',
            default => '—',
        };
    }

    /**
     * @return self::ACCION_*|null
     */
    public function accionDesdeFiltro(?string $filtro): ?string
    {
        $filtro = trim((string) $filtro);

        if ($filtro === '') {
            return null;
        }

        $permitidos = [
            self::ACCION_CONFIRMAR,
            self::ACCION_PREPARACION,
            self::ACCION_ENVIADO,
            self::ACCION_ENTREGADO,
        ];

        return in_array($filtro, $permitidos, true) ? $filtro : null;
    }

    public function estatusIdParaAccion(string $accion): ?int
    {
        return match ($accion) {
            self::ACCION_CONFIRMAR => EstatusCatalog::PEDIDO_PENDIENTE,
            self::ACCION_PREPARACION => EstatusCatalog::PEDIDO_CONFIRMADO,
            self::ACCION_ENVIADO => EstatusCatalog::PEDIDO_EN_PREPARACION,
            self::ACCION_ENTREGADO => EstatusCatalog::PEDIDO_ENVIADO,
            default => null,
        };
    }

    public function puedeEjecutarAccion(Pedido $pedido): bool
    {
        return $this->motivoBloqueoAccion($pedido) === null;
    }

    public function motivoBloqueoAccion(Pedido $pedido): ?string
    {
        $accion = $this->accionDisponible($pedido);

        if ($accion === null) {
            return null;
        }

        if ($accion !== self::ACCION_CONFIRMAR) {
            return null;
        }

        $pedido->loadMissing(['pago']);

        try {
            $this->validarPagoParaConfirmar($pedido);
        } catch (ValidationException $e) {
            return collect($e->errors())->flatten()->first();
        }

        return null;
    }

    /**
     * @return array<int>
     */
    public function estatusPendientesSeguimiento(): array
    {
        return [
            EstatusCatalog::PEDIDO_PENDIENTE,
            EstatusCatalog::PEDIDO_CONFIRMADO,
            EstatusCatalog::PEDIDO_EN_PREPARACION,
            EstatusCatalog::PEDIDO_ENVIADO,
        ];
    }

    public function confirmar(Pedido $pedido, ?string $comentario = null): Pedido
    {
        $this->exigirAccion($pedido, self::ACCION_CONFIRMAR);

        return DB::transaction(function () use ($pedido, $comentario) {
            $pedido->loadMissing(['pago']);

            $this->validarPagoParaConfirmar($pedido);

            $pago = $pedido->pago;

            if ($pago && (int) $pago->Id_MetodoPago === PedidoService::METODO_TRANSFERENCIA
                && (int) $pago->Id_Estatus === EstatusCatalog::PAGO_PENDIENTE_VERIFICACION) {
                $transaccion = is_array($pago->Transaccion_Json) ? $pago->Transaccion_Json : [];
                $transaccion['estado'] = 'aprobado';

                $pago->update([
                    'Id_Estatus' => EstatusCatalog::PAGO_PAGADO,
                    'Transaccion_Json' => $transaccion,
                ]);
            }

            $pedido->update([
                'Id_Estatus' => EstatusCatalog::PEDIDO_CONFIRMADO,
            ]);

            $this->inventarioPedidoService->descontarPorConfirmacion($pedido);

            $this->registrarHistorial(
                $pedido,
                EstatusCatalog::PEDIDO_CONFIRMADO,
                $comentario ?: 'Pedido confirmado por el equipo de la tienda.'
            );

            return $pedido->fresh(['estatus', 'pago.estatus', 'pago.metodoPago', 'envio.estatus', 'historial.estatus']);
        });
    }

    public function enPreparacion(Pedido $pedido, ?string $comentario = null): Pedido
    {
        $this->exigirAccion($pedido, self::ACCION_PREPARACION);

        return DB::transaction(function () use ($pedido, $comentario) {
            $pedido->update([
                'Id_Estatus' => EstatusCatalog::PEDIDO_EN_PREPARACION,
            ]);

            $this->registrarHistorial(
                $pedido,
                EstatusCatalog::PEDIDO_EN_PREPARACION,
                $comentario ?: 'Pedido en preparación.'
            );

            return $pedido->fresh(['estatus', 'pago.estatus', 'pago.metodoPago', 'envio.estatus', 'historial.estatus']);
        });
    }

    public function marcarEnviado(
        Pedido $pedido,
        string $empresaEnvio,
        string $numeroGuia,
        ?string $comentario = null
    ): Pedido {
        $this->exigirAccion($pedido, self::ACCION_ENVIADO);

        $pedido = DB::transaction(function () use ($pedido, $empresaEnvio, $numeroGuia, $comentario) {
            $pedido->loadMissing('envio');

            $pedido->update([
                'Id_Estatus' => EstatusCatalog::PEDIDO_ENVIADO,
            ]);

            $envio = $pedido->envio;

            if ($envio) {
                $envio->update([
                    'Empresa_Envio' => $empresaEnvio,
                    'Numero_Guia' => $numeroGuia,
                    'Fecha_Envio' => now(),
                    'Id_Estatus' => EstatusCatalog::ENVIO_EN_TRANSITO,
                ]);
            }

            $textoGuia = trim($numeroGuia);

            $this->registrarHistorial(
                $pedido,
                EstatusCatalog::PEDIDO_ENVIADO,
                $comentario ?: 'Pedido enviado. Guía: '.$textoGuia
            );

            return $pedido->fresh(['estatus', 'pago.estatus', 'pago.metodoPago', 'envio.estatus', 'historial.estatus', 'usuario']);
        });

        $this->pedidoService->enviarNotificacionesPedidoEnviado($pedido);

        return $pedido;
    }

    public function marcarEntregado(Pedido $pedido, ?string $comentario = null): Pedido
    {
        $this->exigirAccion($pedido, self::ACCION_ENTREGADO);

        return DB::transaction(function () use ($pedido, $comentario) {
            $pedido->loadMissing(['pago', 'envio']);

            $pedido->update([
                'Id_Estatus' => EstatusCatalog::PEDIDO_ENTREGADO,
            ]);

            if ($pedido->envio) {
                $pedido->envio->update([
                    'Fecha_Entrega' => now(),
                    'Id_Estatus' => EstatusCatalog::ENVIO_ENTREGADO,
                ]);
            }

            $pago = $pedido->pago;

            if ($pago
                && (int) $pago->Id_MetodoPago === PedidoService::METODO_CONTRA_ENTREGA
                && (int) $pago->Id_Estatus === EstatusCatalog::PAGO_PENDIENTE_COBRO) {
                $transaccion = is_array($pago->Transaccion_Json) ? $pago->Transaccion_Json : [];
                $transaccion['cobro'] = 'cobrado_al_entregar';

                $pago->update([
                    'Id_Estatus' => EstatusCatalog::PAGO_PAGADO,
                    'Transaccion_Json' => $transaccion,
                ]);
            }

            $this->registrarHistorial(
                $pedido,
                EstatusCatalog::PEDIDO_ENTREGADO,
                $comentario ?: 'Pedido entregado al cliente.'
            );

            return $pedido->fresh(['estatus', 'pago.estatus', 'pago.metodoPago', 'envio.estatus', 'historial.estatus']);
        });
    }

    private function exigirAccion(Pedido $pedido, string $accionEsperada): void
    {
        $accion = $this->accionDisponible($pedido);

        if ($accion !== $accionEsperada) {
            throw ValidationException::withMessages([
                'pedido' => 'El pedido no admite esta acción en su estado actual.',
            ]);
        }
    }

    private function validarPagoParaConfirmar(Pedido $pedido): void
    {
        $pago = $pedido->pago;

        if (! $pago) {
            throw ValidationException::withMessages([
                'pedido' => 'El pedido no tiene registro de pago.',
            ]);
        }

        $metodo = (int) $pago->Id_MetodoPago;
        $estatusPago = (int) $pago->Id_Estatus;

        if ($estatusPago === EstatusCatalog::PAGO_RECHAZADO) {
            throw ValidationException::withMessages([
                'pedido' => 'No se puede confirmar un pedido con pago rechazado.',
            ]);
        }

        if ($metodo === PedidoService::METODO_TRANSFERENCIA) {
            if ($estatusPago === EstatusCatalog::PAGO_PENDIENTE_COMPROBANTE) {
                throw ValidationException::withMessages([
                    'pedido' => 'Espera a que el cliente suba el comprobante de transferencia.',
                ]);
            }

            if (! in_array($estatusPago, [
                EstatusCatalog::PAGO_PENDIENTE_VERIFICACION,
                EstatusCatalog::PAGO_PAGADO,
            ], true)) {
                throw ValidationException::withMessages([
                    'pedido' => 'El pago por transferencia no está listo para confirmar el pedido.',
                ]);
            }

            return;
        }

        if ($metodo === PedidoService::METODO_TARJETA) {
            if ($estatusPago !== EstatusCatalog::PAGO_PAGADO) {
                throw ValidationException::withMessages([
                    'pedido' => 'Espera la confirmación del pago con tarjeta (Recurrente).',
                ]);
            }

            return;
        }

        if ($metodo === PedidoService::METODO_CONTRA_ENTREGA) {
            if ($estatusPago !== EstatusCatalog::PAGO_PENDIENTE_COBRO) {
                throw ValidationException::withMessages([
                    'pedido' => 'El pago contra entrega no está en el estado esperado.',
                ]);
            }

            return;
        }

        throw ValidationException::withMessages([
            'pedido' => 'Método de pago no reconocido para confirmar el pedido.',
        ]);
    }

    private function registrarHistorial(Pedido $pedido, int $idEstatus, string $comentario): void
    {
        PedidoHistorial::create([
            'Id_Pedido' => $pedido->Id_Pedido,
            'Id_Estatus' => $idEstatus,
            'Comentario' => $comentario,
            'Fecha_Cambio' => now(),
        ]);
    }
}
