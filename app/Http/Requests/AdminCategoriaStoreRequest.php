<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminCategoriaStoreRequest extends FormRequest
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
            'Cate_Nombre' => ['required', 'string', 'max:200'],
            'Cate_Slug' => ['required', 'string', 'max:200', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:tb_categoria,Cate_Slug'],
            'Cate_Descripcion' => ['required', 'string'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Cate_Nombre.required' => 'Indica el nombre de la categoría.',
            'Cate_Slug.required' => 'Indica la URL (slug) de la categoría.',
            'Cate_Slug.unique' => 'Ese slug ya está en uso por otra categoría.',
            'Cate_Slug.regex' => 'El slug solo puede usar minúsculas, números y guiones.',
            'Cate_Descripcion.required' => 'Escribe la descripción de la categoría.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.max' => 'La imagen no puede superar 5 MB.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->route('admin.categorias.create')
                ->withErrors($validator)
                ->withInput()
        );
    }
}
