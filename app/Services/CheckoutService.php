<?php

namespace App\Services;

use App\Models\Direccion;
use App\Models\MetodoPago;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutService
{
    /**
     * Direcciones del usuario autenticado con municipio y departamento.
     */
    public function direccionesEntrega(?int $idUsuario = null): Collection
    {
        $idUsuario ??= (int) Auth::user()->Id_Usuario;

        return Direccion::query()
            ->where('Id_Usuario', $idUsuario)
            ->with(['usuario', 'municipio.departamento'])
            ->orderBy('Id_Direccion')
            ->get();
    }

    /**
     * Métodos de pago activos en catálogo (tb_metodopago) para el checkout.
     * Atributo dinámico plantilla: tarjeta | transferencia | efectivo | generico
     */
    public function metodosPago(): Collection
    {
        return MetodoPago::query()
            ->orderBy('Id_MetodoPago')
            ->get()
            ->map(function (MetodoPago $metodo) {
                $metodo->setAttribute('plantilla', $this->plantillaMetodoPago($metodo));

                return $metodo;
            });
    }

    protected function plantillaMetodoPago(MetodoPago $metodo): string
    {
        return match ((int) $metodo->Id_MetodoPago) {
            1 => 'tarjeta',
            2 => 'transferencia',
            3 => 'efectivo',
            default => $this->plantillaPorDescripcion($metodo->MetPag_Descripcion),
        };
    }

    protected function plantillaPorDescripcion(string $descripcion): string
    {
        $desc = Str::lower(Str::ascii($descripcion));

        if (str_contains($desc, 'tarjeta')) {
            return 'tarjeta';
        }

        if (str_contains($desc, 'transferencia')) {
            return 'transferencia';
        }

        if (str_contains($desc, 'efectivo') || str_contains($desc, 'contra entrega')) {
            return 'efectivo';
        }

        return 'generico';
    }
}
