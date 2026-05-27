<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Pedido;
use App\Http\Requests\CheckoutStoreRequest;
use App\Services\BoletaPagoService;
use App\Services\CarritoService;
use App\Services\CheckoutService;
use App\Services\EnvioService;
use App\Services\PedidoService;
use App\Services\RecurrentePaymentService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class CarritoController extends Controller
{
    public function __construct(
        protected CarritoService $carritoService,
        protected CheckoutService $checkoutService,
        protected BoletaPagoService $boletaPagoService,
        protected PedidoService $pedidoService,
        protected RecurrentePaymentService $recurrentePaymentService,
        protected EnvioService $envioService
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
        $departamentos = Departamento::query()
            ->with(['municipios' => fn ($query) => $query->orderBy('Nom_Municipio')])
            ->orderBy('Nom_Departamento')
            ->get();
        $municipiosPorDepartamento = $departamentos->mapWithKeys(fn ($departamento) => [
            $departamento->Id_Departamento => $departamento->municipios->map(fn ($municipio) => [
                'id' => $municipio->Id_Municipio,
                'nombre' => $municipio->Nom_Municipio,
            ])->values(),
        ]);
        $metodosPago = $this->checkoutService->metodosPago();
        $pedidosPendientesBoleta = $this->boletaPagoService->pedidosPendientesDeBoleta();
        $lineasCarrito = $this->carritoService->detallesCarrito();
        $subtotalCarrito = $lineasCarrito->sum(
            fn ($linea) => (float) $linea->Precio * (int) $linea->Cantidad
        );
        $envioCarrito = $this->envioService->calcularCosto($subtotalCarrito, $lineasCarrito);
        $totalCarrito = $subtotalCarrito + $envioCarrito;

        return view('cart.checkout', compact(
            'usuario',
            'direcciones',
            'departamentos',
            'municipiosPorDepartamento',
            'metodosPago',
            'pedidosPendientesBoleta',
            'lineasCarrito',
            'subtotalCarrito',
            'envioCarrito',
            'totalCarrito'
        ));
    }

    public function storeDireccion(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'direccion' => ['required', 'string', 'max:200'],
            'id_departamento' => ['required', 'integer', 'exists:tb_departamento,Id_Departamento'],
            'id_municipio' => ['required', 'integer', 'exists:tb_municipio,Id_Municipio'],
        ]);

        $departamento = Departamento::query()
            ->with('municipios')
            ->findOrFail((int) $validated['id_departamento']);

        $idMunicipio = (int) $validated['id_municipio'];
        $municipioPertenece = $departamento->municipios
            ->contains(fn ($municipio) => (int) $municipio->Id_Municipio === $idMunicipio);

        if (! $municipioPertenece) {
            return back()
                ->withErrors([
                    'id_municipio' => 'El municipio seleccionado no pertenece al departamento.',
                ], 'direccion')
                ->withInput()
                ->with('abrir_modal_direccion', true);
        }

        $direccion = Auth::user()->direcciones()->create([
            'Direccion' => $validated['direccion'],
            'Id_Municipio' => $idMunicipio,
        ]);

        return redirect()
            ->route('cart.checkout')
            ->with('success', 'Dirección registrada correctamente.')
            ->with('id_direccion_reciente', $direccion->Id_Direccion);
    }

    public function store(CheckoutStoreRequest $request): RedirectResponse
    {
        $idMetodoPago = (int) $request->validated('id_metodo_pago');
        $idUsuario = (int) Auth::user()->Id_Usuario;
        $lockKey = "checkout:usuario:{$idUsuario}";

        $lock = Cache::lock($lockKey, 10);

        if (! $lock->get()) {
            return redirect()
                ->route('cart.checkout')
                ->withErrors([
                    'id_metodo_pago' => 'Ya estamos procesando tu compra. Espera unos segundos e intenta de nuevo.',
                ]);
        }

        try {
            $pedido = null;

            if ($idMetodoPago === PedidoService::METODO_TARJETA) {
                $pedidoPendiente = $this->pedidoService->pedidoTarjetaPendientePorUsuario($idUsuario);
                $carritoConProductos = $this->carritoService->detallesCarrito($idUsuario)->isNotEmpty();

                if ($pedidoPendiente) {
                    if ($carritoConProductos) {
                        // Carrito nuevo distinto al pedido abandonado: cancelar el viejo y crear uno nuevo.
                        $this->pedidoService->cancelarPedidoTarjetaPendiente($pedidoPendiente, restaurarCarrito: false);
                    } else {
                        $pedido = $pedidoPendiente;
                    }
                }
            }

            if (! $pedido) {
                $pedido = $this->pedidoService->crearDesdeCheckout(
                    (int) $request->validated('id_direccion'),
                    $idMetodoPago
                );
            }

            if ($idMetodoPago === PedidoService::METODO_TARJETA) {
                try {
                    $checkoutUrl = $this->recurrentePaymentService->createCheckoutUrl($pedido);

                    return redirect()->away($checkoutUrl);
                } catch (RequestException|RuntimeException $exception) {
                    return redirect()
                        ->route('cart.checkout')
                        ->withErrors([
                            'id_metodo_pago' => 'No pudimos iniciar el checkout de tarjeta en Recurrente. Intenta de nuevo.',
                        ])
                        ->with('abrir_metodo_pago', $idMetodoPago);
                }
            }

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
        } finally {
            optional($lock)->release();
        }
    }

    public function recurrenteSuccess(int $pedido): RedirectResponse
    {
        return redirect()
            ->route('cart.checkout')
            ->with('success', 'Regresaste desde Recurrente. Estamos validando el estado del pago de tu pedido.')
            ->with('pedido_referencia', $pedido);
    }

    public function recurrenteCancel(int $pedido): RedirectResponse
    {
        $pedidoModel = Pedido::query()
            ->where('Id_Pedido', $pedido)
            ->where('Id_Usuario', (int) Auth::user()->Id_Usuario)
            ->first();

        if ($pedidoModel) {
            $this->pedidoService->cancelarPedidoTarjetaPendiente($pedidoModel, restaurarCarrito: true);
        }

        return redirect()
            ->route('cart.checkout')
            ->with('success', 'Pago cancelado. Los productos de ese intento volvieron a tu carrito. Puedes editarlos y volver a pagar.')
            ->with('abrir_metodo_pago', PedidoService::METODO_TARJETA);
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
