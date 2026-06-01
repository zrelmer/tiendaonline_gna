<?php

namespace App\Services;

use App\Models\Pedido;
use App\Support\EstatusCatalog;

class AdminRevenueChartService
{
    /**
     * Ingresos por mes (últimos 12 meses), excluyendo pedidos cancelados.
     *
     * @return array{labels: list<string>, series: list<float>}
     */
    public function lastTwelveMonths(): array
    {
        $start = now()->subMonths(11)->startOfMonth();

        $totalsByMonth = Pedido::query()
            ->where('Id_Estatus', '!=', EstatusCatalog::PEDIDO_CANCELADO)
            ->where('created_at', '>=', $start)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(Ped_TotalPrecio) as total')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->keyBy(fn ($row) => sprintf('%04d-%02d', (int) $row->year, (int) $row->month));

        $labels = [];
        $series = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);
            $key = $month->format('Y-m');

            $labels[] = ucfirst($month->locale('es')->isoFormat('MMM'));
            $series[] = round((float) ($totalsByMonth->get($key)?->total ?? 0), 2);
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    /**
     * Datos para gráfico de barras: ingresos y cantidad de pedidos por mes.
     *
     * @return array{
     *     labels: list<string>,
     *     series: list<array{name: string, data: list<float|int>}>
     * }
     */
    public function earningBarChart(): array
    {
        $start = now()->subMonths(11)->startOfMonth();

        $totalsByMonth = Pedido::query()
            ->where('Id_Estatus', '!=', EstatusCatalog::PEDIDO_CANCELADO)
            ->where('created_at', '>=', $start)
            ->selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                SUM(Ped_TotalPrecio) as total,
                COUNT(*) as pedidos
            ')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->keyBy(fn ($row) => sprintf('%04d-%02d', (int) $row->year, (int) $row->month));

        $labels = [];
        $ingresos = [];
        $pedidos = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);
            $key = $month->format('Y-m');
            $row = $totalsByMonth->get($key);

            $labels[] = ucfirst($month->locale('es')->isoFormat('MMM'));
            $ingresos[] = round((float) ($row?->total ?? 0), 2);
            $pedidos[] = (int) ($row?->pedidos ?? 0);
        }

        return [
            'labels' => $labels,
            'series' => [
                ['name' => 'Ingresos', 'data' => $ingresos],
                ['name' => 'Pedidos', 'data' => $pedidos],
            ],
        ];
    }
}
