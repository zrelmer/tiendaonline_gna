<?php

namespace App\Services;

use App\Models\ListaDeseo;
use App\Models\Producto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ListaDeseoService
{
    public function idUsuarioAutenticado(): int
    {
        return (int) Auth::user()->Id_Usuario;
    }

    /**
     * Lista de deseos en el formato que usa cart.js (localStorage).
     */
    public function itemsParaFrontend(?int $idUsuario = null): array
    {
        $idUsuario ??= $this->idUsuarioAutenticado();

        $filas = ListaDeseo::query()
            ->where('Id_Usuario', $idUsuario)
            ->with(['producto.imagenes'])
            ->get();

        return $this->formatearFilas($filas);
    }

    /**
     * Fusiona favoritos del navegador (invitado) con la BD al iniciar sesión.
     */
    public function sincronizarDesdeCliente(array $itemsCliente, ?int $idUsuario = null): array
    {
        $idUsuario ??= $this->idUsuarioAutenticado();

        foreach ($itemsCliente as $item) {
            $idProducto = (int) ($item['id'] ?? 0);

            if ($idProducto < 1) {
                continue;
            }

            ListaDeseo::firstOrCreate([
                'Id_Usuario' => $idUsuario,
                'Id_Producto' => $idProducto,
            ]);
        }

        return $this->itemsParaFrontend($idUsuario);
    }

    public function agregarProducto(int $idProducto, ?int $idUsuario = null): array
    {
        $idUsuario ??= $this->idUsuarioAutenticado();

        Producto::query()->findOrFail($idProducto);

        ListaDeseo::firstOrCreate([
            'Id_Usuario' => $idUsuario,
            'Id_Producto' => $idProducto,
        ]);

        return $this->itemsParaFrontend($idUsuario);
    }

    public function eliminarProducto(int $idProducto, ?int $idUsuario = null): array
    {
        $idUsuario ??= $this->idUsuarioAutenticado();

        ListaDeseo::query()
            ->where('Id_Usuario', $idUsuario)
            ->where('Id_Producto', $idProducto)
            ->delete();

        return $this->itemsParaFrontend($idUsuario);
    }

    /**
     * @param  Collection<int, ListaDeseo>  $filas
     */
    protected function formatearFilas(Collection $filas): array
    {
        return $filas->map(function (ListaDeseo $fila) {
            $producto = $fila->producto;
            $imagen = $producto->imagenes->sortBy('orden')->first();
            $imagenUrl = $imagen
                ? asset($imagen->url)
                : asset('storage/products/default.png');

            return [
                'id' => (int) $producto->Id_Producto,
                'nombre' => $producto->Prod_Nombre,
                'precio' => (float) $producto->Prod_Precio,
                'imagen' => $imagenUrl,
                'url' => route('product.details', [
                    'idproducto' => $producto->Id_Producto,
                    'slug_producto' => $producto->Prod_Slug,
                ]),
            ];
        })->values()->all();
    }
}
