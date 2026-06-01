<?php

namespace App\View\Composers;

use App\Models\Categoria;
use App\Models\Cotizacion;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;
use App\Services\AdminBestSellingProductsService;
use App\Services\AdminRecentOrdersService;
use App\Services\AdminRecentPagosService;
use App\Services\AdminRevenueChartService;
use App\Services\AdminTareasPendientesService;
use App\Services\AdminVisitorsChartService;
use App\Support\EstatusCatalog;
use Illuminate\View\View;

class AdminLayoutComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'totalIngresos' => (float) Pedido::query()
                ->where('Id_Estatus', '!=', EstatusCatalog::PEDIDO_CANCELADO)
                ->sum('Ped_TotalPrecio'),
            'totalPedidos' => Pedido::query()->count(),
            'totalProductos' => Producto::query()->where('Prod_Activo', 1)->count(),
            'totalUsuarios' => Usuario::query()->where('Id_Rol', Usuario::ROL_USUARIO)->count(),
            'pedidosPendientes' => Pedido::query()
                ->whereIn('Id_Estatus', [
                    EstatusCatalog::PEDIDO_PENDIENTE,
                    EstatusCatalog::PEDIDO_CONFIRMADO,
                    EstatusCatalog::PEDIDO_EN_PREPARACION,
                ])
                ->count(),
            'cotizacionesPendientes' => Cotizacion::query()
                ->whereIn('Id_Estatus', [
                    EstatusCatalog::COTIZACION_SOLICITUD_RECIBIDA,
                    EstatusCatalog::COTIZACION_EN_REVISION,
                ])
                ->count(),
            'categorias' => Categoria::query()
                ->withCount(['productos' => fn ($query) => $query->where('Prod_Activo', 1)])
                ->orderBy('Cate_Nombre')
                ->get(),
            'revenueChart' => app(AdminRevenueChartService::class)->lastTwelveMonths(),
            'earningChart' => app(AdminRevenueChartService::class)->earningBarChart(),
            'productosMasVendidos' => app(AdminBestSellingProductsService::class)->top(5),
            'pedidosRecientes' => app(AdminRecentOrdersService::class)->latest(5),
            'pagosRecientes' => app(AdminRecentPagosService::class)->latest(5),
            'visitorsChart' => app(AdminVisitorsChartService::class)->pedidosPorEstatus(),
            'tareasPendientes' => app(AdminTareasPendientesService::class)->list(5),
        ]);
    }
}
