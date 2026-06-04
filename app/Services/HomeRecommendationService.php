<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\CarritoDetalle;
use App\Models\DetallePedido;
use App\Models\ListaDeseo;
use App\Models\Producto;
use App\Support\EstatusCatalog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class HomeRecommendationService
{
    /**
     * @param  array<int, int>  $excluirIds
     * @return Collection<int, Producto>
     */
    public function recomendadosParaHome(?int $idUsuario, array $excluirIds, int $limite = 5): Collection
    {
        $limite = max(1, $limite);
        $excluirIds = $this->normalizarIds($excluirIds);

        $seleccionados = $idUsuario
            ? $this->recomendadosPersonalizados($idUsuario, $excluirIds, $limite)
            : collect();

        if ($seleccionados->count() < $limite) {
            $idsYaElegidos = $seleccionados
                ->pluck('Id_Producto')
                ->merge($excluirIds)
                ->all();

            $complemento = $this->fallbackGlobal(
                $idsYaElegidos,
                $limite - $seleccionados->count()
            );

            $seleccionados = $seleccionados->concat($complemento);
        }

        return $seleccionados
            ->unique('Id_Producto')
            ->take($limite)
            ->values();
    }

    /**
     * @param  array<int, int>  $excluirIds
     * @return Collection<int, Producto>
     */
    protected function recomendadosPersonalizados(int $idUsuario, array $excluirIds, int $limite): Collection
    {
        [$categoriasPeso, $marcasPeso] = $this->preferenciasUsuario($idUsuario);

        if ($categoriasPeso === [] && $marcasPeso === []) {
            return collect();
        }

        $consulta = $this->consultaBaseProductos($excluirIds);

        $this->aplicarOrdenPorPreferencias($consulta, $categoriasPeso, $marcasPeso);

        return $consulta->take($limite)->get();
    }

    /**
     * @return array{0: array<int, int>, 1: array<int, int>}
     */
    protected function preferenciasUsuario(int $idUsuario): array
    {
        $categorias = [];
        $marcas = [];

        $detallesPedido = DetallePedido::query()
            ->whereHas('pedido', function (Builder $query) use ($idUsuario) {
                $query->where('Id_Usuario', $idUsuario)
                    ->where('Id_Estatus', '!=', EstatusCatalog::PEDIDO_CANCELADO);
            })
            ->with(['producto:Id_Producto,Id_Categoria,Id_Marca'])
            ->get(['Id_Producto', 'DetaPed_Cantidad']);

        foreach ($detallesPedido as $detalle) {
            $cantidad = max(1, (int) $detalle->DetaPed_Cantidad);
            $this->sumarPreferenciaProducto($categorias, $marcas, $detalle->producto, 3 * $cantidad);
        }

        $deseos = ListaDeseo::query()
            ->where('Id_Usuario', $idUsuario)
            ->with(['producto:Id_Producto,Id_Categoria,Id_Marca'])
            ->get(['Id_Producto']);

        foreach ($deseos as $deseo) {
            $this->sumarPreferenciaProducto($categorias, $marcas, $deseo->producto, 2);
        }

        $carrito = Carrito::query()
            ->where('Id_Usuario', $idUsuario)
            ->first();

        if ($carrito) {
            $lineasCarrito = CarritoDetalle::query()
                ->where('Id_Carrito', $carrito->Id_Carrito)
                ->with(['producto:Id_Producto,Id_Categoria,Id_Marca'])
                ->get(['Id_Producto', 'Cantidad']);

            foreach ($lineasCarrito as $linea) {
                $cantidad = max(1, (int) $linea->Cantidad);
                $this->sumarPreferenciaProducto($categorias, $marcas, $linea->producto, 1 * $cantidad);
            }
        }

        arsort($categorias);
        arsort($marcas);

        return [$categorias, $marcas];
    }

    /**
     * @param  array<int, int>  $categorias
     * @param  array<int, int>  $marcas
     */
    protected function sumarPreferenciaProducto(array &$categorias, array &$marcas, ?Producto $producto, int $puntos): void
    {
        if (! $producto || $puntos < 1) {
            return;
        }

        if ($producto->Id_Categoria) {
            $idCategoria = (int) $producto->Id_Categoria;
            $categorias[$idCategoria] = ($categorias[$idCategoria] ?? 0) + $puntos;
        }

        if ($producto->Id_Marca) {
            $idMarca = (int) $producto->Id_Marca;
            $marcas[$idMarca] = ($marcas[$idMarca] ?? 0) + $puntos;
        }
    }

    /**
     * @param  array<int, int>  $categoriasPeso
     * @param  array<int, int>  $marcasPeso
     */
    protected function aplicarOrdenPorPreferencias(Builder $consulta, array $categoriasPeso, array $marcasPeso): void
    {
        $consulta->withAvg('comentarios', 'Rating')
            ->withCount('carritodetalles');

        $cuandoCategoria = [];
        $bindingsCategoria = [];

        foreach ($categoriasPeso as $idCategoria => $peso) {
            $cuandoCategoria[] = 'WHEN tb_producto.Id_Categoria = ? THEN ?';
            $bindingsCategoria[] = $idCategoria;
            $bindingsCategoria[] = $peso;
        }

        $cuandoMarca = [];
        $bindingsMarca = [];

        foreach ($marcasPeso as $idMarca => $peso) {
            $cuandoMarca[] = 'WHEN tb_producto.Id_Marca = ? THEN ?';
            $bindingsMarca[] = $idMarca;
            $bindingsMarca[] = $peso;
        }

        $partesScore = ['0'];
        $bindingsScore = [];

        if ($cuandoCategoria !== []) {
            $partesScore[] = 'CASE '.implode(' ', $cuandoCategoria).' ELSE 0 END';
            $bindingsScore = array_merge($bindingsScore, $bindingsCategoria);
        }

        if ($cuandoMarca !== []) {
            $partesScore[] = 'CASE '.implode(' ', $cuandoMarca).' ELSE 0 END';
            $bindingsScore = array_merge($bindingsScore, $bindingsMarca);
        }

        if (count($partesScore) > 1) {
            $consulta->orderByRaw(
                '('.implode(' + ', $partesScore).') DESC',
                $bindingsScore
            );
        }

        $consulta->orderByDesc('comentarios_avg_rating')
            ->orderByDesc('carritodetalles_count')
            ->orderByDesc('tb_producto.Id_Producto');
    }

    /**
     * @param  array<int, int>  $excluirIds
     * @return Collection<int, Producto>
     */
    protected function fallbackGlobal(array $excluirIds, int $limite): Collection
    {
        if ($limite < 1) {
            return collect();
        }

        return $this->consultaBaseProductos($excluirIds)
            ->withAvg('comentarios', 'Rating')
            ->withCount('carritodetalles')
            ->orderByDesc('comentarios_avg_rating')
            ->orderByDesc('carritodetalles_count')
            ->orderByDesc('Id_Producto')
            ->take($limite)
            ->get();
    }

    /**
     * @param  array<int, int>  $excluirIds
     */
    protected function consultaBaseProductos(array $excluirIds): Builder
    {
        $consulta = Producto::query()
            ->with(['imagenes', 'comentarios', 'categoria'])
            ->where('Prod_Activo', 1);

        if ($excluirIds !== []) {
            $consulta->whereNotIn('Id_Producto', $excluirIds);
        }

        return $consulta;
    }

    /**
     * @param  array<int, int>  $ids
     * @return array<int, int>
     */
    protected function normalizarIds(array $ids): array
    {
        return array_values(array_unique(array_filter(
            array_map('intval', $ids),
            fn (int $id) => $id > 0
        )));
    }
}
