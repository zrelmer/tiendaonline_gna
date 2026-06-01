<?php

namespace App\Services;

use App\Models\Municipio;
use Illuminate\Validation\ValidationException;

class AdminMunicipioService
{
    /**
     * @param  array<string, mixed>  $datos
     */
    public function crear(array $datos): Municipio
    {
        return Municipio::query()->create([
            'Nom_Municipio' => $datos['Nom_Municipio'],
            'Id_Departamento' => $datos['Id_Departamento'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function actualizar(Municipio $municipio, array $datos): Municipio
    {
        $municipio->update([
            'Nom_Municipio' => $datos['Nom_Municipio'],
            'Id_Departamento' => $datos['Id_Departamento'],
        ]);

        return $municipio->fresh();
    }

    public function eliminar(Municipio $municipio): void
    {
        if ($municipio->direcciones()->exists()) {
            throw ValidationException::withMessages([
                'municipio' => 'No se puede eliminar: el municipio tiene una o más direcciones asociadas.',
            ]);
        }

        $municipio->delete();
    }
}
