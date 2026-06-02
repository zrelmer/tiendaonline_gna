<?php

namespace App\Services;

use App\Models\Marca;
use Illuminate\Validation\ValidationException;

class AdminMarcaService
{
    /**
     * @param  array<string, mixed>  $datos
     */
    public function crear(array $datos): Marca
    {
        return Marca::query()->create([
            'Nom_Marca' => $datos['Nom_Marca'],
            'slug_Marca' => $datos['slug_Marca'],
            'Descrip_Marca' => $datos['Descrip_Marca'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function actualizar(Marca $marca, array $datos): Marca
    {
        $marca->update([
            'Nom_Marca' => $datos['Nom_Marca'],
            'slug_Marca' => $datos['slug_Marca'],
            'Descrip_Marca' => $datos['Descrip_Marca'],
        ]);

        return $marca->fresh();
    }

    public function eliminar(Marca $marca): void
    {
        if ($marca->productos()->exists()) {
            throw ValidationException::withMessages([
                'marca' => 'No se puede eliminar: la marca tiene uno o más productos asociados.',
            ]);
        }

        $marca->delete();
    }
}
