<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminDepartamentoStoreRequest extends FormRequest
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
            'Nom_Departamento' => ['required', 'string', 'max:200', 'unique:tb_departamento,Nom_Departamento'],
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
        throw new HttpResponseException(
            redirect()
                ->route('admin.departamentos.create')
                ->withErrors($validator)
                ->withInput()
        );
    }
}
