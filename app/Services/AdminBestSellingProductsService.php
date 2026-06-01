<?php

namespace App\Services;

use App\Models\DetallePedido;
use App\Models\Producto;
use App\Support\EstatusCatalog;
use Illuminate\Support\Collection;

class AdminBestSellingProductsService
{
    /**
     * Productos con más unidades vendidas (pedidos no cancelados).
     *
     * @return Collection<int, object{
     *     producto: Producto,
     *     unidades_vendidas: int,
     *     pedidos_count: int,
     *     monto_total: float,
     *     ultima_venta: ?\Carbon\Carbon,
     *     imagen_url: string,
     *     stock: int,
     * }>
     */
    public function top(int $limit = 5): Collection
    {
        $rows = DetallePedido::query()
            ->join('tb_pedido', 'tb_pedido.Id_Pedido', '=', 'tb_detallepedido.Id_Pedido')
            ->where('tb_pedido.Id_Estatus', '!=', EstatusCatalog::PEDIDO_CANCELADO)
            ->selectRaw('
                tb_detallepedido.Id_Producto,
                SUM(tb_detallepedido.DetaPed_Cantidad) as unidades_vendidas,
                SUM(tb_detallepedido.DetaPed_SubTotal) as monto_total,
                COUNT(DISTINCT tb_detallepedido.Id_Pedido) as pedidos_count,
                MAX(tb_pedido.created_at) as ultima_venta
            ')
            ->groupBy('tb_detallepedido.Id_Producto')
            ->orderByDesc('unidades_vendidas')
            ->limit($limit)
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $productos = Producto::query()
            ->with([
                'imagenes' => fn ($query) => $query->orderBy('orden'),
                'inventario',
            ])
            ->whereIn('Id_Producto', $rows->pluck('Id_Producto'))
            ->get()
            ->keyBy('Id_Producto');

        return $rows
            ->map(function ($row) use ($productos) {
                $producto = $productos->get($row->Id_Producto);

                if (! $producto) {
                    return null;
                }

                $imagen = $producto->imagenes->first();

                return (object) [
                    'producto' => $producto,
                    'unidades_vendidas' => (int) $row->unidades_vendidas,
                    'pedidos_count' => (int) $row->pedidos_count,
                    'monto_total' => (float) $row->monto_total,
                    'ultima_venta' => $row->ultima_venta ? \Carbon\Carbon::parse($row->ultima_venta) : null,
                    'imagen_url' => $imagen ? asset($imagen->url) : asset('storage/products/default.png'),
                    'stock' => (int) ($producto->inventario?->Stock ?? 0),
                ];
            })
            ->filter()
            ->values();
    }
}
