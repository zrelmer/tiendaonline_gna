<?php

namespace App\Http\Requests;

use App\Services\AdminInventarioService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AdminInventarioAjusteRequest extends FormRequest
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
        $tipo = (string) $this->input('tipo');

        return [
            'tipo' => [
                'required',
                'string',
                Rule::in([
                    AdminInventarioService::TIPO_ENTRADA,
                    AdminInventarioService::TIPO_SALIDA,
                    AdminInventarioService::TIPO_FIJAR,
                ]),
            ],
            'cantidad' => [
                'required',
                'integer',
                $tipo === AdminInventarioService::TIPO_FIJAR ? 'min:0' : 'min:1',
                'max:999999',
            ],
            'comentario' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tipo.required' => 'Selecciona el tipo de ajuste.',
            'tipo.in' => 'El tipo de ajuste no es válido.',
            'cantidad.required' => 'Indica la cantidad del ajuste.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad indicada no es válida para este tipo de ajuste.',
            'cantidad.max' => 'La cantidad supera el límite permitido.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->route('admin.inventario.ajustar', $this->route('producto'))
                ->withErrors($validator, 'inventario')
                ->withInput()
        );
    }
}
