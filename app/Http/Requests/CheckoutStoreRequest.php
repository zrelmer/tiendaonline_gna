<?php

namespace App\Http\Requests;

use App\Services\PedidoService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CheckoutStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $idUsuario = (int) Auth::user()->Id_Usuario;

        return [
            'id_direccion' => [
                'required',
                'integer',
                Rule::exists('tb_direccion', 'Id_Direccion')->where('Id_Usuario', $idUsuario),
            ],
            'id_metodo_pago' => [
                'required',
                'integer',
                Rule::in([
                    PedidoService::METODO_TRANSFERENCIA,
                    PedidoService::METODO_CONTRA_ENTREGA,
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id_direccion.required' => 'Debes seleccionar una dirección de entrega.',
            'id_direccion.exists' => 'La dirección seleccionada no es válida.',
            'id_metodo_pago.required' => 'Debes seleccionar un método de pago.',
            'id_metodo_pago.in' => 'Selecciona transferencia bancaria o pago contra entrega.',
        ];
    }
}
