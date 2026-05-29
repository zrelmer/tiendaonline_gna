<?php

namespace App\Services;

use App\Mail\PedidoGeneradoMail;
use App\Models\DetallePedido;
use App\Models\Direccion;
use App\Models\Envio;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoHistorial;
use App\Support\EstatusCatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PedidoService
{
    public const METODO_TARJETA = 1;

    public const METODO_TRANSFERENCIA = 2;

    public const METODO_CONTRA_ENTREGA = 3;

    public function __construct(
        protected CarritoService $carritoService,
        protected EnvioService $envioService,
        protected WhatsAppService $whatsAppService
    ) {}

    public function crearDesdeCheckout(
        int $idDireccion,
        int $idMetodoPago,
        ?int $idUsuario = null
    ): Pedido
    {
        $configPago = match ($idMetodoPago) {
            self::METODO_TARJETA => [
                'id_estatus_pago' => EstatusCatalog::PAGO_PENDIENTE_VERIFICACION,
                'transaccion' => [
                    'tipo' => 'tarjeta',
                    'estado' => 'checkout_pendiente',
                    'gateway' => 'recurrente',
                ],
                'historial' => 'Pedido registrado. Redirigiendo a Recurrente para completar el pago.',
            ],
            self::METODO_TRANSFERENCIA => [
                'id_estatus_pago' => EstatusCatalog::PAGO_PENDIENTE_COMPROBANTE,
                'transaccion' => [
                    'tipo' => 'transferencia',
                    'estado' => 'pendiente_comprobante',
                ],
                'historial' => 'Pedido registrado. Pendiente de transferencia bancaria y comprobante de pago.',
            ],
            self::METODO_CONTRA_ENTREGA => [
                'id_estatus_pago' => EstatusCatalog::PAGO_PENDIENTE_COBRO,
                'transaccion' => [
                    'tipo' => 'contra_entrega',
                    'cobro' => 'pendiente_al_entregar',
                ],
                'historial' => 'Pedido registrado. Pago contra entrega pendiente al momento de la entrega.',
            ],
            default => throw ValidationException::withMessages([
                'id_metodo_pago' => 'Método de pago no disponible.',
            ]),
        };

        return $this->crearPedido($idDireccion, $idMetodoPago, $configPago, $idUsuario);
    }

    public function pedidoTarjetaPendientePorUsuario(int $idUsuario): ?Pedido
    {
        return Pedido::query()
            ->where('Id_Usuario', $idUsuario)
            ->where('Id_Estatus', EstatusCatalog::PEDIDO_PENDIENTE)
            ->whereHas('pago', function ($query) {
                $query->where('Id_MetodoPago', self::METODO_TARJETA)
                    ->where('Id_Estatus', EstatusCatalog::PAGO_PENDIENTE_VERIFICACION);
            })
            ->latest('Id_Pedido')
            ->first();
    }

    /**
     * Cancela un pedido de tarjeta no pagado. Opcionalmente devuelve sus líneas al carrito.
     */
    public function cancelarPedidoTarjetaPendiente(Pedido $pedido, bool $restaurarCarrito = true): void
    {
        $this->cancelarPedidoPendienteCliente($pedido, $restaurarCarrito);
    }

    public function esGestionablePorCliente(Pedido $pedido): bool
    {
        $pedido->loadMissing('pago');

        if ((int) $pedido->Id_Estatus !== EstatusCatalog::PEDIDO_PENDIENTE) {
            return false;
        }

        if ($pedido->pago && (int) $pedido->pago->Id_Estatus === EstatusCatalog::PAGO_PAGADO) {
            return false;
        }

        return true;
    }

    /**
     * Cancela un pedido pendiente de confirmación iniciado por el cliente.
     */
    public function cancelarPedidoPendienteCliente(Pedido $pedido, bool $restaurarCarrito = true): void
    {
        if ((int) $pedido->Id_Estatus === EstatusCatalog::PEDIDO_CANCELADO) {
            return;
        }

        if (! $this->esGestionablePorCliente($pedido)) {
            throw ValidationException::withMessages([
                'pedido' => 'Solo puedes cancelar pedidos pendientes de confirmación.',
            ]);
        }

        DB::transaction(function () use ($pedido, $restaurarCarrito) {
            $pedido->loadMissing(['pago', 'detalle']);

            if ($pedido->pago) {
                $transaccion = is_array($pedido->pago->Transaccion_Json)
                    ? $pedido->pago->Transaccion_Json
                    : [];

                $pedido->pago->update([
                    'Id_Estatus' => EstatusCatalog::PAGO_RECHAZADO,
                    'Transaccion_Json' => [
                        ...$transaccion,
                        'estado' => 'cancelado',
                        'cancelado_en' => now()->toIso8601String(),
                    ],
                ]);
            }

            $pedido->update([
                'Id_Estatus' => EstatusCatalog::PEDIDO_CANCELADO,
            ]);

            PedidoHistorial::create([
                'Id_Pedido' => $pedido->Id_Pedido,
                'Id_Estatus' => EstatusCatalog::PEDIDO_CANCELADO,
                'Comentario' => $restaurarCarrito
                    ? 'Pedido cancelado por el cliente. Productos devueltos al carrito.'
                    : 'Pedido cancelado por el cliente.',
                'Fecha_Cambio' => now(),
            ]);

            if ($restaurarCarrito) {
                $this->restaurarCarritoDesdePedido($pedido);
            }
        });
    }

    /**
     * @param  array<int, array{id_detalle: int, cantidad: int}>  $items
     */
    public function actualizarPedidoPendiente(Pedido $pedido, int $idDireccion, array $items): Pedido
    {
        if (! $this->esGestionablePorCliente($pedido)) {
            throw ValidationException::withMessages([
                'pedido' => 'Solo puedes editar pedidos pendientes de confirmación.',
            ]);
        }

        $idUsuario = (int) $pedido->Id_Usuario;

        $direccion = Direccion::query()
            ->where('Id_Direccion', $idDireccion)
            ->where('Id_Usuario', $idUsuario)
            ->with(['municipio.departamento'])
            ->firstOrFail();

        return DB::transaction(function () use ($pedido, $direccion, $items) {
            $pedido->loadMissing(['detalle.producto.categoria', 'envio']);

            $detallesPorId = $pedido->detalle->keyBy('Id_DetallePedido');

            foreach ($items as $item) {
                $idDetalle = (int) $item['id_detalle'];
                $cantidad = (int) $item['cantidad'];
                $detalle = $detallesPorId->get($idDetalle);

                if (! $detalle) {
                    continue;
                }

                if ($cantidad <= 0) {
                    $detalle->delete();

                    continue;
                }

                $precio = (float) $detalle->DetaPed_Precio;
                $detalle->update([
                    'DetaPed_Cantidad' => $cantidad,
                    'DetaPed_SubTotal' => round($precio * $cantidad, 2),
                ]);
            }

            $pedido->load('detalle.producto.categoria');

            if ($pedido->detalle->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'El pedido debe incluir al menos un producto.',
                ]);
            }

            $subtotal = $pedido->detalle->sum(
                fn ($linea) => (float) $linea->DetaPed_SubTotal
            );
            $envio = $this->envioService->calcularCosto($subtotal, $pedido->detalle);
            $total = round($subtotal + $envio, 2);

            $pedido->update([
                'Id_Direccion' => $direccion->Id_Direccion,
                'Ped_TotalPrecio' => $total,
            ]);

            if ($pedido->envio) {
                $pedido->envio->update([
                    'Direccion_Envio' => $this->textoDireccionEnvio($direccion),
                ]);
            }

            PedidoHistorial::create([
                'Id_Pedido' => $pedido->Id_Pedido,
                'Id_Estatus' => EstatusCatalog::PEDIDO_PENDIENTE,
                'Comentario' => 'Pedido actualizado por el cliente (dirección e ítems).',
                'Fecha_Cambio' => now(),
            ]);

            return $pedido->fresh([
                'estatus',
                'pago.metodoPago',
                'detalle.producto.imagenes',
                'direccion.municipio.departamento',
                'envio',
            ]);
        });
    }

    public function restaurarCarritoDesdePedido(Pedido $pedido, ?int $idUsuario = null): void
    {
        $idUsuario ??= (int) $pedido->Id_Usuario;
        $pedido->loadMissing('detalle');

        foreach ($pedido->detalle as $linea) {
            $this->carritoService->agregarProducto(
                (int) $linea->Id_Producto,
                (int) $linea->DetaPed_Cantidad,
                (float) $linea->DetaPed_Precio,
                $idUsuario
            );
        }
    }

    protected function crearPedido(
        int $idDireccion,
        int $idMetodoPago,
        array $configPago,
        ?int $idUsuario = null
    ): Pedido {
        $idUsuario ??= $this->carritoService->idUsuarioAutenticado();

        $pedido = DB::transaction(function () use ($idDireccion, $idMetodoPago, $configPago, $idUsuario) {
            $detalles = $this->carritoService->detallesCarrito($idUsuario);

            if ($detalles->isEmpty()) {
                throw ValidationException::withMessages([
                    'carrito' => 'Tu carrito está vacío. Agrega productos antes de confirmar.',
                ]);
            }

            $direccion = Direccion::query()
                ->where('Id_Direccion', $idDireccion)
                ->where('Id_Usuario', $idUsuario)
                ->with(['municipio.departamento'])
                ->firstOrFail();

            $subtotal = $detalles->sum(
                fn ($linea) => (float) $linea->Precio * (int) $linea->Cantidad
            );
            $envio = $this->envioService->calcularCosto($subtotal, $detalles);
            $total = round($subtotal + $envio, 2);

            $pedido = Pedido::create([
                'Id_Usuario' => $idUsuario,
                'Id_Direccion' => $direccion->Id_Direccion,
                'Ped_Numero' => $this->generarNumeroPedido(),
                'Ped_TotalPrecio' => $total,
                'Id_Estatus' => EstatusCatalog::PEDIDO_PENDIENTE,
            ]);

            foreach ($detalles as $linea) {
                $cantidad = (int) $linea->Cantidad;
                $precio = (float) $linea->Precio;

                DetallePedido::create([
                    'Id_Pedido' => $pedido->Id_Pedido,
                    'Id_Producto' => $linea->Id_Producto,
                    'DetaPed_Cantidad' => $cantidad,
                    'DetaPed_Precio' => $precio,
                    'DetaPed_SubTotal' => round($precio * $cantidad, 2),
                ]);
            }

            $transaccionId = null;
            $transaccionJson = $configPago['transaccion'];
            $idEstatusPago = $configPago['id_estatus_pago'];
            $comentarioHistorial = $configPago['historial'];

            Pago::create([
                'Id_Pedido' => $pedido->Id_Pedido,
                'Id_MetodoPago' => $idMetodoPago,
                'Transaccion_Id' => $transaccionId,
                'Transaccion_Json' => $transaccionJson,
                'Id_Estatus' => $idEstatusPago,
            ]);

            Envio::create([
                'Id_Pedido' => $pedido->Id_Pedido,
                'Direccion_Envio' => $this->textoDireccionEnvio($direccion),
                'Empresa_Envio' => null,
                'Numero_Guia' => null,
                'Fecha_Envio' => now(),
                'Fecha_Entrega' => null,
                'Id_Estatus' => EstatusCatalog::ENVIO_PENDIENTE,
            ]);

            PedidoHistorial::create([
                'Id_Pedido' => $pedido->Id_Pedido,
                'Id_Estatus' => EstatusCatalog::PEDIDO_PENDIENTE,
                'Comentario' => $comentarioHistorial,
                'Fecha_Cambio' => now(),
            ]);

            $this->carritoService->vaciarCarrito($idUsuario);

            return $pedido->load(['detalle.producto', 'pago', 'envio']);
        });

        $pedido->loadMissing(['usuario', 'pago.metodoPago', 'detalle.producto']);

        // Tarjeta: notificación solo cuando Recurrente confirma el pago (webhook).
        if ($idMetodoPago !== self::METODO_TARJETA) {
            $this->enviarNotificacionesPedido($pedido);
        }

        return $pedido;
    }

    protected function generarNumeroPedido(): string
    {
        do {
            $numero = 'PED-'.strtoupper(Str::random(8));
        } while (Pedido::query()->where('Ped_Numero', $numero)->exists());

        return $numero;
    }

    protected function textoDireccionEnvio(Direccion $direccion): string
    {
        $partes = array_filter([
            $direccion->Direccion,
            $direccion->municipio?->Nom_Municipio,
            $direccion->municipio?->departamento?->Nom_Departamento,
        ]);

        return Str::limit(implode(', ', $partes), 200, '');
    }

    public function enviarNotificacionesPedido(Pedido $pedido, bool $pagoConfirmado = false): void
    {
        $this->enviarCorreoPedido($pedido, $pagoConfirmado);
        $this->whatsAppService->sendPedido($pedido, $pagoConfirmado);
    }

    public function enviarCorreoPedido(Pedido $pedido, bool $pagoConfirmado = false): void
    {
        $pedido->loadMissing(['usuario', 'pago.metodoPago', 'detalle.producto']);
        $correo = $pedido->usuario?->Usu_Correo;

        if (! $correo) {
            return;
        }

        Mail::to($correo)->send(new PedidoGeneradoMail($pedido, $pagoConfirmado));
    }
}
