<?php

namespace App\Services;

use App\Models\Departamento;
use App\Models\Direccion;
use App\Models\Usuario;
use Illuminate\Validation\ValidationException;

class DireccionService
{
    public function validarMunicipioEnDepartamento(int $idDepartamento, int $idMunicipio): void
    {
        $departamento = Departamento::query()
            ->with('municipios')
            ->findOrFail($idDepartamento);

        $municipioPertenece = $departamento->municipios
            ->contains(fn ($municipio) => (int) $municipio->Id_Municipio === $idMunicipio);

        if (! $municipioPertenece) {
            throw ValidationException::withMessages([
                'id_municipio' => 'El municipio seleccionado no pertenece al departamento.',
            ]);
        }
    }

    public function crearParaUsuario(Usuario $usuario, string $direccion, int $idDepartamento, int $idMunicipio): Direccion
    {
        $this->validarMunicipioEnDepartamento($idDepartamento, $idMunicipio);

        return $usuario->direcciones()->create([
            'Direccion' => $direccion,
            'Id_Municipio' => $idMunicipio,
        ]);
    }

    public function actualizar(Direccion $direccion, string $texto, int $idDepartamento, int $idMunicipio): Direccion
    {
        $this->validarMunicipioEnDepartamento($idDepartamento, $idMunicipio);

        $direccion->update([
            'Direccion' => $texto,
            'Id_Municipio' => $idMunicipio,
        ]);

        return $direccion->fresh(['municipio.departamento']);
    }

    public function eliminar(Direccion $direccion): void
    {
        if ($direccion->pedido()->exists()) {
            throw ValidationException::withMessages([
                'direccion' => 'No puedes eliminar una dirección asociada a un pedido.',
            ]);
        }

        $direccion->delete();
    }
}
