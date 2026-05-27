<?php

namespace App\Services;

use App\Models\Producto;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EnvioService
{
    public function umbralEnvioGratis(): float
    {
        return (float) config('shipping.umbral_envio_gratis', 300);
    }

    public function costoEnvioBase(): float
    {
        return (float) config('shipping.costo_envio', 35);
    }

    /**
     * @param  Collection<int, mixed>  $lineas  CarritoDetalle o DetallePedido con relación producto
     */
    public function calcularCosto(float $subtotal, Collection $lineas): float
    {
        if (! $this->requiereEnvioFisico($lineas)) {
            return 0.0;
        }

        return $subtotal < $this->umbralEnvioGratis()
            ? $this->costoEnvioBase()
            : 0.0;
    }

    /**
     * @param  Collection<int, mixed>  $lineas
     */
    public function requiereEnvioFisico(Collection $lineas): bool
    {
        foreach ($lineas as $linea) {
            $producto = $linea->producto ?? null;

            if ($producto instanceof Producto && ! $this->esProductoDigital($producto)) {
                return true;
            }
        }

        return false;
    }

    public function esProductoDigital(Producto $producto): bool
    {
        $producto->loadMissing('categoria');

        $categoria = $producto->categoria;

        if (! $categoria) {
            return false;
        }

        $slug = Str::lower(Str::ascii((string) $categoria->Cate_Slug));
        $slugsDigitales = array_map(
            fn (string $value) => Str::lower(Str::ascii($value)),
            (array) config('shipping.categorias_digitales_slug', [])
        );

        if (in_array($slug, $slugsDigitales, true)) {
            return true;
        }

        $textoCategoria = Str::lower(Str::ascii(implode(' ', array_filter([
            $categoria->Cate_Nombre,
            $categoria->Cate_Slug,
            $categoria->Cate_Descripcion,
        ]))));

        foreach ((array) config('shipping.palabras_clave_digitales', []) as $palabra) {
            $palabraNormalizada = Str::lower(Str::ascii((string) $palabra));

            if ($palabraNormalizada !== '' && str_contains($textoCategoria, $palabraNormalizada)) {
                return true;
            }
        }

        return false;
    }
}
