<?php

namespace App\Http\Requests;

use App\Models\Direccion;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DashboardDireccionUpdateRequest extends DireccionUpdateRequest
{
    protected function failedValidation(Validator $validator): void
    {
        /** @var Direccion|null $direccion */
        $direccion = $this->route('direccion');

        throw new HttpResponseException(
            redirect()
                ->route('dashboard')
                ->withErrors($validator, 'direccion')
                ->withInput()
                ->with('tab', 'addresses')
                ->with('abrir_modal_direccion', 'editar')
                ->with('editar_direccion_id', $direccion?->Id_Direccion)
        );
    }
}
