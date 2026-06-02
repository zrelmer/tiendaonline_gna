<?php

namespace App\Services;

use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use App\Support\EstatusCatalog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AdminInventarioVentasService
{
    public function __construct(
        protected AdminInventarioService $adminInventarioService
    ) {}

    /**
     * @return array{
     *     filas: LengthAwarePaginator<int, object>,
     *     totales: array{unidades: int, monto: float, productos: int, pedidos: int},
     * }
     */
    public function reportePorProducto(
        ?string $fechaDesde,
        ?string $fechaHasta,
        string $terminoBusqueda = '',
        int $porPagina = 20
    ): array {
        $fechas = $this->adminInventarioService->fechasDesdeRequest($fechaDesde, $fechaHasta);
        $terminoBusqueda = trim($terminoBusqueda);

        $filas = $this->consultaAgrupada($fechas['desde'], $fechas['hasta'], $terminoBusqueda);

        $pagina = max(1, (int) request()->input('page', 1));
        $items = $filas->forPage($pagina, $porPagina)->values();

        $paginador = new LengthAwarePaginator(
            $items,
            $filas->count(),
            $porPagina,
            $pagina,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return [
            'filas' => $paginador,
            'totales' => [
                'unidades' => (int) $filas->sum(fn ($fila) => $fila->unidades_vendidas),
                'monto' => (float) $filas->sum(fn ($fila) => $fila->monto_total),
                'productos' => $filas->count(),
                'pedidos' => $this->contarPedidosEnPeriodo($fechas['desde'], $fechas['hasta']),
            ],
        ];
    }

    /**
     * @return Collection<int, object{
     *     producto: Producto,
     *     unidades_vendidas: int,
     *     pedidos_count: int,
     *     monto_total: float,
     *     ultima_venta: ?Carbon,
     *     stock: int,
     *     disponible: int,
     * }>
     */
    public function top(int $limit = 5, ?string $fechaDesde = null, ?string $fechaHasta = null): Collection
    {
        $fechas = $this->adminInventarioService->fechasDesdeRequest($fechaDesde, $fechaHasta);

        return $this->consultaAgrupada($fechas['desde'], $fechas['hasta'], '')
            ->take($limit)
            ->values();
    }

    /**
     * @return Collection<int, object>
     */
    private function consultaAgrupada(?string $desde, ?string $hasta, string $termino): Collection
    {
        $query = DetallePedido::query()
            ->join('tb_pedido', 'tb_pedido.Id_Pedido', '=', 'tb_detallepedido.Id_Pedido')
            ->join('tb_producto', 'tb_producto.Id_Producto', '=', 'tb_detallepedido.Id_Producto')
            ->where('tb_pedido.Id_Estatus', '!=', EstatusCatalog::PEDIDO_CANCELADO);

        if ($desde !== null && $desde !== '') {
            $query->whereDate('tb_pedido.created_at', '>=', $desde);
        }

        if ($hasta !== null && $hasta !== '') {
            $query->whereDate('tb_pedido.created_at', '<=', $hasta);
        }

        if ($termino !== '') {
            $like = '%'.$termino.'%';
            $query->where(function ($q) use ($termino, $like) {
                $q->where('tb_producto.Prod_Nombre', 'like', $like)
                    ->orWhere('tb_producto.Prod_Slug', 'like', $like);

                if (ctype_digit($termino)) {
                    $q->orWhere('tb_producto.Id_Producto', (int) $termino);
                }
            });
        }

        $rows = $query
            ->selectRaw('
                tb_detallepedido.Id_Producto,
                SUM(tb_detallepedido.DetaPed_Cantidad) as unidades_vendidas,
                SUM(tb_detallepedido.DetaPed_SubTotal) as monto_total,
                COUNT(DISTINCT tb_detallepedido.Id_Pedido) as pedidos_count,
                MAX(tb_pedido.created_at) as ultima_venta
            ')
            ->groupBy('tb_detallepedido.Id_Producto')
            ->orderByDesc('unidades_vendidas')
            ->orderBy('tb_detallepedido.Id_Producto')
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $productos = Producto::query()
            ->with(['inventario', 'categoria'])
            ->whereIn('Id_Producto', $rows->pluck('Id_Producto'))
            ->get()
            ->keyBy('Id_Producto');

        return $rows
            ->map(function ($row) use ($productos) {
                $producto = $productos->get($row->Id_Producto);

                if (! $producto) {
                    return null;
                }

                $stock = (int) ($producto->inventario?->Stock ?? 0);
                $reservado = (int) ($producto->inventario?->Stock_Reservado ?? 0);

                return (object) [
                    'producto' => $producto,
                    'unidades_vendidas' => (int) $row->unidades_vendidas,
                    'pedidos_count' => (int) $row->pedidos_count,
                    'monto_total' => (float) $row->monto_total,
                    'ultima_venta' => $row->ultima_venta ? Carbon::parse($row->ultima_venta) : null,
                    'stock' => $stock,
                    'disponible' => max(0, $stock - $reservado),
                ];
            })
            ->filter()
            ->values();
    }

    private function contarPedidosEnPeriodo(?string $desde, ?string $hasta): int
    {
        $query = Pedido::query()
            ->where('Id_Estatus', '!=', EstatusCatalog::PEDIDO_CANCELADO);

        if ($desde !== null && $desde !== '') {
            $query->whereDate('created_at', '>=', $desde);
        }

        if ($hasta !== null && $hasta !== '') {
            $query->whereDate('created_at', '<=', $hasta);
        }

        return $query->count();
    }
}
