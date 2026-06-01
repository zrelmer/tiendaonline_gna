<?php

namespace App\Services;

use App\Models\Departamento;
use Illuminate\Validation\ValidationException;

class AdminDepartamentoService
{
    /**
     * @param  array<string, mixed>  $datos
     */
    public function crear(array $datos): Departamento
    {
        return Departamento::query()->create([
            'Nom_Departamento' => $datos['Nom_Departamento'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function actualizar(Departamento $departamento, array $datos): Departamento
    {
        $departamento->update([
            'Nom_Departamento' => $datos['Nom_Departamento'],
        ]);

        return $departamento->fresh();
    }

    public function eliminar(Departamento $departamento): void
    {
        if ($departamento->municipios()->exists()) {
            throw ValidationException::withMessages([
                'departamento' => 'No se puede eliminar: el departamento tiene uno o más municipios asociados.',
            ]);
        }

        $departamento->delete();
    }
}
