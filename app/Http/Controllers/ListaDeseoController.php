<?php

namespace App\Http\Controllers;

use App\Services\ListaDeseoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListaDeseoController extends Controller
{
    public function __construct(
        protected ListaDeseoService $listaDeseoService
    ) {}

    public function index()
    {
        return view('listadeseo.index');
    }

    public function items(): JsonResponse
    {
        return response()->json([
            'items' => $this->listaDeseoService->itemsParaFrontend(),
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['nullable', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:tb_producto,Id_Producto'],
        ]);

        $items = $this->listaDeseoService->sincronizarDesdeCliente(
            $validated['items'] ?? []
        );

        return response()->json([
            'items' => $items,
            'message' => 'Lista de deseos sincronizada',
        ]);
    }

    public function storeItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_producto' => ['required', 'integer', 'exists:tb_producto,Id_Producto'],
        ]);

        $items = $this->listaDeseoService->agregarProducto(
            (int) $validated['id_producto']
        );

        return response()->json([
            'items' => $items,
            'message' => 'Producto agregado a favoritos',
        ]);
    }

    public function destroyItem(int $idProducto): JsonResponse
    {
        $items = $this->listaDeseoService->eliminarProducto($idProducto);

        return response()->json([
            'items' => $items,
            'message' => 'Producto eliminado de favoritos',
        ]);
    }
}
