<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\CarritoDetalle;
use App\Models\Producto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CarritoService
{
    /**
     * Obtiene el Id_Usuario del usuario autenticado (PK real, no el correo).
     */
    public function idUsuarioAutenticado(): int
    {
        return (int) Auth::user()->Id_Usuario;
    }

    /**
     * Un usuario solo tiene un carrito (unique en Id_Usuario).
     */
    public function obtenerOCrearCarrito(?int $idUsuario = null): Carrito
    {
        $idUsuario ??= $this->idUsuarioAutenticado();

        return Carrito::firstOrCreate(['Id_Usuario' => $idUsuario]);
    }

    /**
     * Líneas del carrito en BD (para crear pedido).
     */
    public function detallesCarrito(?int $idUsuario = null)
    {
        $carrito = $this->obtenerOCrearCarrito($idUsuario);

        return $carrito->detallescarrito()
            ->with(['producto.imagenes'])
            ->get();
    }

    public function vaciarCarrito(?int $idUsuario = null): void
    {
        $carrito = $this->obtenerOCrearCarrito($idUsuario);

        CarritoDetalle::query()
            ->where('Id_Carrito', $carrito->Id_Carrito)
            ->delete();
    }

    /**
     * Lista líneas del carrito en el formato que usa cart.js (localStorage).
     */
    public function itemsParaFrontend(?int $idUsuario = null): array
    {
        $carrito = $this->obtenerOCrearCarrito($idUsuario);

        $detalles = $carrito->detallescarrito()
            ->with(['producto.imagenes'])
            ->get();

        return $this->formatearDetalles($detalles);
    }

    /**
     * Fusiona el carrito del invitado (localStorage) con la BD al iniciar sesión.
     * Si el producto ya existe en BD, suma cantidades. No usar en cada recarga de página.
     */
    public function sincronizarDesdeCliente(array $itemsCliente, ?int $idUsuario = null): array
    {
        $carrito = $this->obtenerOCrearCarrito($idUsuario);

        foreach ($itemsCliente as $item) {
            $idProducto = (int) ($item['id'] ?? 0);
            $cantidad = (int) ($item['cantidad'] ?? 0);

            if ($idProducto < 1 || $cantidad < 1) {
                continue;
            }

            $precio = isset($item['precio'])
                ? (float) $item['precio']
                : $this->precioProducto($idProducto);

            $detalle = CarritoDetalle::query()
                ->where('Id_Carrito', $carrito->Id_Carrito)
                ->where('Id_Producto', $idProducto)
                ->first();

            if ($detalle) {
                $detalle->Cantidad += $cantidad;
                $detalle->save();
            } else {
                CarritoDetalle::create([
                    'Id_Carrito' => $carrito->Id_Carrito,
                    'Id_Producto' => $idProducto,
                    'Cantidad' => $cantidad,
                    'Precio' => $precio,
                ]);
            }
        }

        return $this->itemsParaFrontend($idUsuario);
    }

    /**
     * Agrega cantidad a un producto (o crea la línea si no existe).
     */
    public function agregarProducto(int $idProducto, int $cantidad, ?float $precio = null, ?int $idUsuario = null): array
    {
        $carrito = $this->obtenerOCrearCarrito($idUsuario);
        $precio ??= $this->precioProducto($idProducto);

        $detalle = CarritoDetalle::query()
            ->where('Id_Carrito', $carrito->Id_Carrito)
            ->where('Id_Producto', $idProducto)
            ->first();

        if ($detalle) {
            $detalle->Cantidad += $cantidad;
            $detalle->save();
        } else {
            CarritoDetalle::create([
                'Id_Carrito' => $carrito->Id_Carrito,
                'Id_Producto' => $idProducto,
                'Cantidad' => $cantidad,
                'Precio' => $precio,
            ]);
        }

        return $this->itemsParaFrontend($idUsuario);
    }

    /**
     * Establece la cantidad exacta de una línea (botones +/- en la vista carrito).
     */
    public function actualizarCantidad(int $idProducto, int $cantidad, ?int $idUsuario = null): array
    {
        $carrito = $this->obtenerOCrearCarrito($idUsuario);

        $detalle = CarritoDetalle::query()
            ->where('Id_Carrito', $carrito->Id_Carrito)
            ->where('Id_Producto', $idProducto)
            ->firstOrFail();

        if ($cantidad < 1) {
            CarritoDetalle::query()
                ->whereKey($detalle->getKey())
                ->delete();
        } else {
            $detalle->Cantidad = $cantidad;
            $detalle->save();
        }

        return $this->itemsParaFrontend($idUsuario);
    }

    /**
     * Elimina un producto del carrito en BD.
     */
    public function eliminarProducto(int $idProducto, ?int $idUsuario = null): array
    {
        $carrito = $this->obtenerOCrearCarrito($idUsuario);

        CarritoDetalle::query()
            ->where('Id_Carrito', $carrito->Id_Carrito)
            ->where('Id_Producto', $idProducto)
            ->delete();

        return $this->itemsParaFrontend($idUsuario);
    }

    protected function precioProducto(int $idProducto): float
    {
        $producto = Producto::query()->findOrFail($idProducto);

        return (float) $producto->Prod_Precio;
    }

    /**
     * @param  Collection<int, CarritoDetalle>  $detalles
     */
    protected function formatearDetalles(Collection $detalles): array
    {
        return $detalles->map(function (CarritoDetalle $detalle) {
            $producto = $detalle->producto;
            $imagen = $producto->imagenes->sortBy('orden')->first();
            $imagenUrl = $imagen
                ? asset($imagen->url)
                : asset('storage/products/default.png');

            return [
                'id' => (int) $detalle->Id_Producto,
                'nombre' => $producto->Prod_Nombre,
                'precio' => (float) $detalle->Precio,
                'imagen' => $imagenUrl,
                'url' => route('product.details', [
                    'idproducto' => $producto->Id_Producto,
                    'slug_producto' => $producto->Prod_Slug,
                ]),
                'cantidad' => (int) $detalle->Cantidad,
            ];
        })->values()->all();
    }
}
