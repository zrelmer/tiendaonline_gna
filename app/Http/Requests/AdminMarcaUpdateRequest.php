<?php

namespace App\Http\Requests;

use App\Models\Marca;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AdminMarcaUpdateRequest extends FormRequest
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
        /** @var Marca $marca */
        $marca = $this->route('marca');

        return [
            'Nom_Marca' => ['required', 'string', 'max:200'],
            'slug_Marca' => [
                'required',
                'string',
                'max:200',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('tb_marca', 'slug_Marca')->ignore($marca->Id_Marca, 'Id_Marca'),
            ],
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
        /** @var Marca $marca */
        $marca = $this->route('marca');

        throw new HttpResponseException(
            redirect()
                ->route('admin.marcas.edit', $marca)
                ->withErrors($validator)
                ->withInput()
        );
    }
}
