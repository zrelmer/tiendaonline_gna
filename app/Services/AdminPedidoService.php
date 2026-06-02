<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\PedidoHistorial;
use App\Support\EstatusCatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminPedidoService
{
    public function __construct(
        protected InventarioPedidoService $inventarioPedidoService
    ) {}

    public function ocultar(Pedido $pedido): Pedido
    {
        if ((int) $pedido->Id_Estatus !== EstatusCatalog::PEDIDO_CANCELADO) {
            throw ValidationException::withMessages([
                'pedido' => 'Solo se pueden ocultar pedidos cancelados por el usuario.',
            ]);
        }

        if ($pedido->Ped_OcultoAdmin) {
            throw ValidationException::withMessages([
                'pedido' => 'Este pedido ya está oculto del listado administrativo.',
            ]);
        }

        $pedido->update([
            'Ped_OcultoAdmin' => true,
        ]);

        return $pedido->fresh();
    }

    public function puedeCancelar(Pedido $pedido): bool
    {
        return $this->motivoNoCancelar($pedido) === null;
    }

    public function motivoNoCancelar(Pedido $pedido): ?string
    {
        $estatusId = (int) $pedido->Id_Estatus;

        if ($estatusId === EstatusCatalog::PEDIDO_CANCELADO) {
            return 'El pedido ya está cancelado.';
        }

        if ($estatusId === EstatusCatalog::PEDIDO_ENTREGADO) {
            return 'No se puede cancelar un pedido ya entregado.';
        }

        return null;
    }

    /**
     * @return array{pedido: Pedido, repuso_stock: bool}
     */
    public function cancelar(Pedido $pedido, ?string $comentario = null): array
    {
        if ($this->motivoNoCancelar($pedido) !== null) {
            throw ValidationException::withMessages([
                'pedido' => $this->motivoNoCancelar($pedido),
            ]);
        }

        $teniaDescuento = $this->inventarioPedidoService->yaDescontado($pedido);

        $pedido = DB::transaction(function () use ($pedido, $comentario, $teniaDescuento) {
            $pedido->loadMissing(['pago', 'detalle']);

            $pedido->update([
                'Id_Estatus' => EstatusCatalog::PEDIDO_CANCELADO,
            ]);

            PedidoHistorial::create([
                'Id_Pedido' => $pedido->Id_Pedido,
                'Id_Estatus' => EstatusCatalog::PEDIDO_CANCELADO,
                'Comentario' => $comentario ?: 'Pedido cancelado por el equipo de la tienda.',
                'Fecha_Cambio' => now(),
            ]);

            if ($teniaDescuento) {
                $this->inventarioPedidoService->reponerPorCancelacion($pedido);
            } else {
                $this->inventarioPedidoService->liberarReservaPorPedido($pedido);
            }

            return $pedido->fresh(['estatus', 'pago.estatus', 'pago.metodoPago', 'envio.estatus']);
        });

        return [
            'pedido' => $pedido,
            'repuso_stock' => $teniaDescuento && $this->inventarioPedidoService->yaRepuestoPorCancelacion($pedido),
        ];
    }
}
