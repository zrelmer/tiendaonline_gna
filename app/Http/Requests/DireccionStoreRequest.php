<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DireccionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'direccion' => ['required', 'string', 'max:200'],
            'id_departamento' => ['required', 'integer', 'exists:tb_departamento,Id_Departamento'],
            'id_municipio' => ['required', 'integer', 'exists:tb_municipio,Id_Municipio'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'direccion.required' => 'La dirección es obligatoria.',
            'id_departamento.required' => 'Selecciona un departamento.',
            'id_municipio.required' => 'Selecciona un municipio.',
        ];
    }
}
