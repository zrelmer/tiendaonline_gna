<?php

namespace App\Services;

use App\Models\Pedido;

class AdminVisitorsChartService
{
    /**
     * Distribución de pedidos por estatus (gráfico tipo dona).
     *
     * @return array{labels: list<string>, series: list<int>}
     */
    public function pedidosPorEstatus(): array
    {
        $rows = Pedido::query()
            ->join('tb_estatus', 'tb_pedido.Id_Estatus', '=', 'tb_estatus.Id_Estatus')
            ->selectRaw('tb_estatus.Nom_Estatus as label, COUNT(*) as total')
            ->groupBy('tb_estatus.Id_Estatus', 'tb_estatus.Nom_Estatus')
            ->orderByDesc('total')
            ->get();

        if ($rows->isEmpty()) {
            return [
                'labels' => ['Sin pedidos'],
                'series' => [1],
            ];
        }

        return [
            'labels' => $rows->pluck('label')->all(),
            'series' => $rows->pluck('total')->map(fn ($value) => (int) $value)->all(),
        ];
    }
}
