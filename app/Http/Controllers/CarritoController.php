<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutStoreRequest;
use App\Services\BoletaPagoService;
use App\Services\CarritoService;
use App\Services\CheckoutService;
use App\Services\PedidoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    public function __construct(
        protected CarritoService $carritoService,
        protected CheckoutService $checkoutService,
        protected BoletaPagoService $boletaPagoService,
        protected PedidoService $pedidoService
    ) {}

    /**
     * Vista del carrito (sigue usando cart.js + localStorage para pintar).
     */
    public function index()
    {
        return view('cart.index');
    }

    /**
     * Vista checkout (finalizar compra / pago).
     */
    public function checkout()
    {
        $usuario = Auth::user();
        $direcciones = $this->checkoutService->direccionesEntrega();
        $metodosPago = $this->checkoutService->metodosPago();
        $pedidosPendientesBoleta = $this->boletaPagoService->pedidosPendientesDeBoleta();
        $lineasCarrito = $this->carritoService->detallesCarrito();
        $subtotalCarrito = $lineasCarrito->sum(
            fn ($linea) => (float) $linea->Precio * (int) $linea->Cantidad
        );
        $envioCarrito = $subtotalCarrito < 300 ? 35 : 0;
        $totalCarrito = $subtotalCarrito + $envioCarrito;

        return view('cart.checkout', compact(
            'usuario',
            'direcciones',
            'metodosPago',
            'pedidosPendientesBoleta',
            'lineasCarrito',
            'subtotalCarrito',
            'envioCarrito',
            'totalCarrito'
        ));
    }

    public function store(CheckoutStoreRequest $request): RedirectResponse
    {
        $idMetodoPago = (int) $request->validated('id_metodo_pago');

        $pedido = $this->pedidoService->crearDesdeCheckout(
            (int) $request->validated('id_direccion'),
            $idMetodoPago
        );

        $mensaje = match ($idMetodoPago) {
            PedidoService::METODO_TRANSFERENCIA => 'Pedido '.$pedido->Ped_Numero.' registrado. Realiza tu transferencia y sube el comprobante en la sección Transferencia bancaria.',
            PedidoService::METODO_CONTRA_ENTREGA => 'Pedido '.$pedido->Ped_Numero.' registrado. Pagarás en efectivo al recibir tu compra.',
            default => 'Pedido '.$pedido->Ped_Numero.' registrado correctamente.',
        };

        return redirect()
            ->route('cart.checkout')
            ->with('pedido_creado', true)
            ->with('pedido_numero', $pedido->Ped_Numero)
            ->with('abrir_metodo_pago', $idMetodoPago)
            ->with('success', $mensaje);
    }

    /**
     * GET /cart/items — Devuelve el carrito del usuario en JSON.
     */
    public function items(): JsonResponse
    {
        return response()->json([
            'items' => $this->carritoService->itemsParaFrontend(),
        ]);
    }

    /**
     * POST /cart/sync — Fusiona localStorage con la BD (típico al iniciar sesión).
     */
    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['nullable', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:tb_producto,Id_Producto'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'items.*.precio' => ['nullable', 'numeric', 'min:0'],
        ]);

        $items = $this->carritoService->sincronizarDesdeCliente(
            $validated['items'] ?? []
        );

        return response()->json([
            'items' => $items,
            'message' => 'Carrito sincronizado',
        ]);
    }

    /**
     * POST /cart/items — Agregar producto (suma si ya existe).
     */
    public function storeItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_producto' => ['required', 'integer', 'exists:tb_producto,Id_Producto'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'precio' => ['nullable', 'numeric', 'min:0'],
        ]);

        $items = $this->carritoService->agregarProducto(
            (int) $validated['id_producto'],
            (int) $validated['cantidad'],
            isset($validated['precio']) ? (float) $validated['precio'] : null
        );

        return response()->json([
            'items' => $items,
            'message' => 'Producto agregado al carrito',
        ]);
    }

    /**
     * PATCH /cart/items/{idProducto} — Cantidad exacta (o elimina si cantidad < 1).
     */
    public function updateItem(Request $request, int $idProducto): JsonResponse
    {
        $validated = $request->validate([
            'cantidad' => ['required', 'integer', 'min:0'],
        ]);

        $items = $this->carritoService->actualizarCantidad(
            $idProducto,
            (int) $validated['cantidad']
        );

        return response()->json([
            'items' => $items,
            'message' => 'Cantidad actualizada',
        ]);
    }

    /**
     * DELETE /cart/items/{idProducto}
     */
    public function destroyItem(int $idProducto): JsonResponse
    {
        $items = $this->carritoService->eliminarProducto($idProducto);

        return response()->json([
            'items' => $items,
            'message' => 'Producto eliminado del carrito',
        ]);
    }
}
