<?php

namespace App\Support;

use App\Models\Pedido;
use App\Models\Pago;

final class PedidoPagoPresenter
{
    /**
     * @return array{label: string, class: string}
     */
    public static function estadoPago(?Pago $pago): array
    {
        if (! $pago) {
            return [
                'label' => 'Sin registro',
                'class' => 'text-muted',
            ];
        }

        return match ((int) $pago->Id_Estatus) {
            EstatusCatalog::PAGO_PAGADO => [
                'label' => 'Pagado',
                'class' => 'theme-color',
            ],
            EstatusCatalog::PAGO_RECHAZADO => [
                'label' => 'Rechazado',
                'class' => 'text-danger',
            ],
            EstatusCatalog::PAGO_PENDIENTE_COMPROBANTE,
            EstatusCatalog::PAGO_PENDIENTE_VERIFICACION,
            EstatusCatalog::PAGO_PENDIENTE_COBRO => [
                'label' => 'Pendiente',
                'class' => 'text-danger',
            ],
            default => [
                'label' => $pago->estatus?->Nom_Estatus ?? 'Pendiente',
                'class' => 'text-danger',
            ],
        };
    }

    public static function tituloProducto(Pedido $pedido): string
    {
        $lineas = $pedido->detalle;

        if ($lineas->isEmpty()) {
            return 'Pedido sin detalle';
        }

        $primero = $lineas->first()?->producto?->Prod_Nombre;

        if ($lineas->count() === 1) {
            return $primero ?? 'Producto';
        }

        return ($primero ?? 'Productos').' (+'.($lineas->count() - 1).')';
    }

    public static function iconoMetodoPago(?\App\Models\MetodoPago $metodo): string
    {
        return match ((int) ($metodo?->Id_MetodoPago ?? 0)) {
            1 => 'ri-bank-card-line',
            2 => 'ri-bank-line',
            3 => 'ri-money-dollar-box-line',
            default => 'ri-secure-payment-line',
        };
    }

    /**
     * @return array{text: string, class: string}
     */
    public static function montoTransaccion(Pago $pago, float $totalPedido): array
    {
        $monto = 'Q '.number_format($totalPedido, 2);

        return match ((int) $pago->Id_Estatus) {
            EstatusCatalog::PAGO_PAGADO => [
                'text' => '+'.$monto,
                'class' => 'success',
            ],
            EstatusCatalog::PAGO_RECHAZADO => [
                'text' => $monto,
                'class' => 'lost',
            ],
            default => [
                'text' => $monto,
                'class' => 'lost',
            ],
        };
    }

    public static function claseFilaTransaccion(int $indice, bool $esUltima): string
    {
        $clases = ['', 'td-color-1', 'td-color-2', 'td-color-3', 'td-color-4'];

        $clase = $clases[$indice % count($clases)];

        if ($esUltima) {
            $clase .= $clase !== '' ? ' pb-0' : 'pb-0';
        }

        return trim($clase);
    }
}
