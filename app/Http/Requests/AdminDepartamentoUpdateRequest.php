<?php

namespace App\Http\Requests;

use App\Models\Departamento;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AdminDepartamentoUpdateRequest extends FormRequest
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
        /** @var Departamento $departamento */
        $departamento = $this->route('departamento');

        return [
            'Nom_Departamento' => [
                'required',
                'string',
                'max:200',
                Rule::unique('tb_departamento', 'Nom_Departamento')->ignore($departamento->Id_Departamento, 'Id_Departamento'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Nom_Departamento.required' => 'Indica el nombre del departamento.',
            'Nom_Departamento.unique' => 'Ese nombre de departamento ya está registrado.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        /** @var Departamento $departamento */
        $departamento = $this->route('departamento');

        throw new HttpResponseException(
            redirect()
                ->route('admin.departamentos.edit', $departamento)
                ->withErrors($validator)
                ->withInput()
        );
    }
}
