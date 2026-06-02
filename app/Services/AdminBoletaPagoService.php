<?php

namespace App\Services;

use App\Models\BoletaPago;
use App\Models\Pedido;
use App\Models\PedidoHistorial;
use App\Support\EstatusCatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdminBoletaPagoService
{
    public function __construct(
        protected AdminPedidoSeguimientoService $adminPedidoSeguimientoService
    ) {}

    public function puedeAprobar(BoletaPago $boleta): bool
    {
        return $this->motivoNoAprobable($boleta) === null;
    }

    public function puedeRechazar(BoletaPago $boleta): bool
    {
        return $this->motivoNoRechazable($boleta) === null;
    }

    public function motivoNoAprobable(BoletaPago $boleta): ?string
    {
        $boleta->loadMissing(['pedido.pago', 'pedido.estatus']);

        $pedido = $boleta->pedido;

        if (! $pedido) {
            return 'No se encontró el pedido asociado a esta boleta.';
        }

        if ($pedido->Ped_OcultoAdmin) {
            return 'El pedido está oculto del panel administrativo.';
        }

        $pago = $pedido->pago;

        if (! $pago || (int) $pago->Id_MetodoPago !== PedidoService::METODO_TRANSFERENCIA) {
            return 'Este pedido no está asociado a transferencia bancaria.';
        }

        $estatusPago = (int) $pago->Id_Estatus;

        if ($estatusPago === EstatusCatalog::PAGO_PAGADO) {
            return 'El pago ya fue aprobado.';
        }

        if ($estatusPago === EstatusCatalog::PAGO_RECHAZADO) {
            return 'El pago fue rechazado.';
        }

        if ($estatusPago !== EstatusCatalog::PAGO_PENDIENTE_VERIFICACION) {
            return 'El comprobante no está pendiente de verificación.';
        }

        if ((int) $pedido->Id_Estatus !== EstatusCatalog::PEDIDO_PENDIENTE) {
            return 'El pedido ya fue confirmado o no está pendiente.';
        }

        if (! $boleta->archivoDisponible()) {
            return 'El archivo del comprobante no está disponible.';
        }

        return null;
    }

    public function motivoNoRechazable(BoletaPago $boleta): ?string
    {
        $boleta->loadMissing(['pedido.pago', 'pedido.estatus']);

        $pedido = $boleta->pedido;

        if (! $pedido) {
            return 'No se encontró el pedido asociado a esta boleta.';
        }

        if ($pedido->Ped_OcultoAdmin) {
            return 'El pedido está oculto del panel administrativo.';
        }

        $pago = $pedido->pago;

        if (! $pago || (int) $pago->Id_MetodoPago !== PedidoService::METODO_TRANSFERENCIA) {
            return 'Este pedido no está asociado a transferencia bancaria.';
        }

        if ((int) $pago->Id_Estatus !== EstatusCatalog::PAGO_PENDIENTE_VERIFICACION) {
            return 'Solo se pueden rechazar comprobantes pendientes de verificación.';
        }

        if ((int) $pedido->Id_Estatus !== EstatusCatalog::PEDIDO_PENDIENTE) {
            return 'El pedido ya fue confirmado o no está pendiente.';
        }

        return null;
    }

    public function aprobar(BoletaPago $boleta, ?string $comentario = null): BoletaPago
    {
        $motivo = $this->motivoNoAprobable($boleta);

        if ($motivo !== null) {
            throw ValidationException::withMessages([
                'boleta' => $motivo,
            ]);
        }

        $pedido = $boleta->pedido;

        $this->adminPedidoSeguimientoService->confirmar(
            $pedido,
            $comentario ?: 'Comprobante de transferencia aprobado. Pedido confirmado.'
        );

        return $boleta->fresh([
            'pedido.usuario',
            'pedido.estatus',
            'pedido.pago.estatus',
            'pedido.pago.metodoPago',
        ]);
    }

    public function rechazar(BoletaPago $boleta, string $motivo): Pedido
    {
        $motivo = trim($motivo);

        $error = $this->motivoNoRechazable($boleta);

        if ($error !== null) {
            throw ValidationException::withMessages([
                'boleta' => $error,
            ]);
        }

        return DB::transaction(function () use ($boleta, $motivo) {
            $boleta->loadMissing(['pedido.pago']);
            $pedido = $boleta->pedido;
            $pago = $pedido->pago;

            if ($boleta->BoletaImagen) {
                Storage::disk('public')->delete($boleta->BoletaImagen);
            }

            $boleta->delete();

            $transaccion = is_array($pago->Transaccion_Json) ? $pago->Transaccion_Json : [];
            $transaccion['estado'] = 'comprobante_rechazado';
            $transaccion['motivo_rechazo'] = $motivo;
            $transaccion['rechazado_en'] = now()->toIso8601String();

            $pago->update([
                'Id_Estatus' => EstatusCatalog::PAGO_PENDIENTE_COMPROBANTE,
                'Transaccion_Json' => $transaccion,
            ]);

            PedidoHistorial::create([
                'Id_Pedido' => $pedido->Id_Pedido,
                'Id_Estatus' => EstatusCatalog::PEDIDO_PENDIENTE,
                'Comentario' => 'Comprobante rechazado: '.$motivo.' Sube un nuevo comprobante de transferencia.',
                'Fecha_Cambio' => now(),
            ]);

            return $pedido->fresh(['usuario', 'estatus', 'pago.estatus', 'pago.metodoPago']);
        });
    }

    public function contarPendientesVerificacion(): int
    {
        return BoletaPago::query()
            ->pendientesVerificacionAdmin()
            ->count();
    }
}
