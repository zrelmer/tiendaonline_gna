<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DashboardCotizacionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_cliente' => ['required', 'string', 'max:200'],
            'nit' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:300'],
            'email' => ['nullable', 'email', 'max:150'],
            'notas' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_producto' => ['nullable', 'integer', 'exists:tb_producto,Id_Producto'],
            'items.*.descripcion' => ['nullable', 'string', 'max:500'],
            'items.*.cantidad' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_cliente.required' => 'Indica el nombre o razón social para la cotización.',
            'items.required' => 'Agrega al menos una línea de producto o descripción.',
            'items.min' => 'Agrega al menos una línea de producto o descripción.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->route('dashboard')
                ->withErrors($validator, 'cotizacion')
                ->withInput()
                ->with('tab', 'quotes')
                ->with('abrir_formulario_cotizacion', true)
        );
    }
}
