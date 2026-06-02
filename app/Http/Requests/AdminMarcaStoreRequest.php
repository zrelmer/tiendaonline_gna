<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminMarcaStoreRequest extends FormRequest
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
            'Nom_Marca' => ['required', 'string', 'max:200'],
            'slug_Marca' => ['required', 'string', 'max:200', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:tb_marca,slug_Marca'],
            'Descrip_Marca' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Nom_Marca.required' => 'Indica el nombre de la marca.',
            'slug_Marca.required' => 'Indica la URL (slug) de la marca.',
            'slug_Marca.unique' => 'Ese slug ya está en uso por otra marca.',
            'slug_Marca.regex' => 'El slug solo puede usar minúsculas, números y guiones.',
            'Descrip_Marca.required' => 'Escribe la descripción de la marca.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->route('admin.marcas.create')
                ->withErrors($validator)
                ->withInput()
        );
    }
}
