<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BoletaPagoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_pedido' => ['required', 'integer', 'exists:tb_pedido,Id_Pedido'],
            'boleta' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'boleta.required' => 'Debes seleccionar un archivo de comprobante.',
            'boleta.mimes' => 'Solo se permiten archivos PNG, JPG o PDF.',
            'boleta.max' => 'El archivo no puede superar 10 MB.',
            'id_pedido.required' => 'Debes seleccionar el pedido asociado al comprobante.',
            'id_pedido.exists' => 'El pedido seleccionado no es válido.',
        ];
    }
}
