<?php

namespace App\Http\Controllers;

use App\Http\Requests\BoletaPagoStoreRequest;
use App\Services\BoletaPagoService;
use Illuminate\Http\RedirectResponse;

class BoletaPagoController extends Controller
{
    public function __construct(
        protected BoletaPagoService $boletaPagoService
    ) {}

    public function store(BoletaPagoStoreRequest $request): RedirectResponse
    {
        $idPedido = (int) $request->validated('id_pedido');

        $this->boletaPagoService->pedidoDelUsuario($idPedido);

        $this->boletaPagoService->guardar(
            $request->file('boleta'),
            $idPedido
        );

        return redirect()
            ->route('cart.checkout')
            ->with('success', 'Comprobante de transferencia subido correctamente.');
    }
}
