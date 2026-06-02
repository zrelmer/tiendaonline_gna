<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminPedidoSeguimientoEnviadoRequest extends FormRequest
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
            'empresa_envio' => ['required', 'string', 'max:200'],
            'numero_guia' => ['required', 'string', 'max:200'],
            'comentario' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'empresa_envio.required' => 'Indica el transportista o empresa de envío.',
            'numero_guia.required' => 'Indica el número de guía.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->route('admin.pedidos.seguimiento', $this->route('pedido'))
                ->withErrors($validator, 'seguimiento')
                ->withInput()
        );
    }
}
