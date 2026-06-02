<?php

namespace App\Http\Requests;

use App\Models\Producto;
use App\Support\EstatusCatalog;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AdminProductoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'Prod_Activo' => $this->boolean('Prod_Activo'),
            'Stock_Reservado' => (int) $this->input('Stock_Reservado', 0),
        ]);

        if ($this->input('Prod_PrecioOferta') === '' || $this->input('Prod_PrecioOferta') === null) {
            $this->merge(['Prod_PrecioOferta' => null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Producto $producto */
        $producto = $this->route('producto');

        return [
            'Prod_Nombre' => ['required', 'string', 'max:200'],
            'Prod_Slug' => [
                'required',
                'string',
                'max:200',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('tb_producto', 'Prod_Slug')->ignore($producto->Id_Producto, 'Id_Producto'),
            ],
            'Id_Categoria' => ['required', 'integer', 'exists:tb_categoria,Id_Categoria'],
            'Id_Marca' => ['required', 'integer', 'exists:tb_marca,Id_Marca'],
            'Id_Estatus' => [
                'required',
                'integer',
                Rule::in([
                    EstatusCatalog::PRODUCTO_ACTIVO,
                    EstatusCatalog::PRODUCTO_INACTIVO,
                    EstatusCatalog::PRODUCTO_AGOTADO,
                    EstatusCatalog::PRODUCTO_PENDIENTE,
                ]),
            ],
            'Prod_Activo' => ['required', 'boolean'],
            'Prod_Descripcion' => ['required', 'string'],
            'Prod_Precio' => ['required', 'numeric', 'min:0'],
            'Prod_PrecioOferta' => ['nullable', 'numeric', 'min:0'],
            'imagenes' => ['nullable', 'array'],
            'imagenes.*' => ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Prod_Nombre.required' => 'Indica el nombre del producto.',
            'Prod_Slug.required' => 'Indica la URL (slug) del producto.',
            'Prod_Slug.unique' => 'Ese slug ya está en uso por otro producto.',
            'Prod_Slug.regex' => 'El slug solo puede usar minúsculas, números y guiones.',
            'Id_Categoria.required' => 'Selecciona una categoría.',
            'Id_Marca.required' => 'Selecciona una marca.',
            'Id_Estatus.required' => 'Selecciona el estatus del producto.',
            'Prod_Descripcion.required' => 'Escribe la descripción del producto.',
            'Prod_Precio.required' => 'Indica el precio de venta.',
            'imagenes.*.image' => 'Cada archivo debe ser una imagen válida.',
            'imagenes.*.max' => 'Cada imagen no puede superar 5 MB.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        /** @var Producto $producto */
        $producto = $this->route('producto');

        throw new HttpResponseException(
            redirect()
                ->route('admin.productos.edit', $producto)
                ->withErrors($validator)
                ->withInput()
        );
    }
}
