<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AdminMunicipioStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'Nom_Municipio' => [
                'required',
                'string',
                'max:200',
                Rule::unique('tb_municipio', 'Nom_Municipio')->where(
                    fn ($query) => $query->where('Id_Departamento', $this->input('Id_Departamento'))
                ),
            ],
            'Id_Departamento' => ['required', 'integer', 'exists:tb_departamento,Id_Departamento'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Nom_Municipio.required' => 'Indica el nombre del municipio.',
            'Nom_Municipio.unique' => 'Ese nombre ya existe en el departamento seleccionado.',
            'Id_Departamento.required' => 'Selecciona un departamento.',
            'Id_Departamento.exists' => 'El departamento seleccionado no es válido.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->route('admin.municipios.create')
                ->withErrors($validator)
                ->withInput()
        );
    }
}
