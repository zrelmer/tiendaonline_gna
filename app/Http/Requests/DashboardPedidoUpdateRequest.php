<?php

namespace App\Http\Requests;

use App\Models\DetallePedido;
use App\Models\Pedido;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DashboardPedidoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pedido = $this->route('pedido');

        return $pedido instanceof Pedido
            && (int) $pedido->Id_Usuario === (int) $this->user()->Id_Usuario;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $idUsuario = (int) $this->user()->Id_Usuario;

        return [
            'id_direccion' => [
                'required',
                'integer',
                Rule::exists('tb_direccion', 'Id_Direccion')->where('Id_Usuario', $idUsuario),
            ],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_detalle' => ['required', 'integer', 'distinct'],
            'items.*.cantidad' => ['required', 'integer', 'min:0', 'max:999'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $pedido = $this->route('pedido');

            if (! $pedido instanceof Pedido) {
                return;
            }

            $items = $this->input('items', []);
            $tieneProductos = collect($items)->contains(
                fn ($item) => (int) ($item['cantidad'] ?? 0) > 0
            );

            if (! $tieneProductos) {
                $validator->errors()->add('items', 'El pedido debe incluir al menos un producto.');

                return;
            }

            $idsDetalle = collect($items)->pluck('id_detalle')->map(fn ($id) => (int) $id);
            $detallesValidos = DetallePedido::query()
                ->where('Id_Pedido', $pedido->Id_Pedido)
                ->whereIn('Id_DetallePedido', $idsDetalle)
                ->count();

            if ($detallesValidos !== $idsDetalle->unique()->count()) {
                $validator->errors()->add('items', 'Uno o más productos no pertenecen a este pedido.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_direccion.required' => 'Selecciona una dirección de envío.',
            'id_direccion.exists' => 'La dirección seleccionada no es válida.',
            'items.required' => 'Debes indicar los productos del pedido.',
            'items.min' => 'Debes indicar los productos del pedido.',
            'items.*.cantidad.min' => 'La cantidad no puede ser negativa.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->route('dashboard')
                ->withErrors($validator)
                ->withInput()
                ->with('tab', 'orders')
        );
    }
}
