<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardPedidoUpdateRequest;
use App\Models\Pedido;
use App\Services\PedidoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DashboardPedidoController extends Controller
{
    public function __construct(
        protected PedidoService $pedidoService
    ) {}

    public function update(DashboardPedidoUpdateRequest $request, Pedido $pedido): RedirectResponse
    {
        try {
            $this->pedidoService->actualizarPedidoPendiente(
                $pedido,
                (int) $request->validated('id_direccion'),
                $request->validated('items'),
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('dashboard')
                ->withErrors($e->errors())
                ->withInput()
                ->with('tab', 'orders');
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Pedido actualizado correctamente.')
            ->with('tab', 'orders');
    }

    public function cancel(Request $request, Pedido $pedido): RedirectResponse
    {
        if ((int) $pedido->Id_Usuario !== (int) $request->user()->Id_Usuario) {
            abort(403);
        }

        try {
            $this->pedidoService->cancelarPedidoPendienteCliente($pedido);
        } catch (ValidationException $e) {
            return redirect()
                ->route('dashboard')
                ->withErrors($e->errors())
                ->with('tab', 'orders');
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Pedido cancelado. Los productos fueron devueltos a tu carrito.')
            ->with('tab', 'orders');
    }
}
