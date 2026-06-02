<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPedidoCancelarRequest;
use App\Http\Requests\AdminPedidoSeguimientoEnviadoRequest;
use App\Models\Pedido;
use App\Models\PedidoHistorial;
use App\Services\AdminPedidoSeguimientoService;
use App\Services\AdminPedidoService;
use App\Support\EstatusCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminPedidoController extends Controller
{
    public function __construct(
        protected AdminPedidoService $adminPedidoService,
        protected AdminPedidoSeguimientoService $adminPedidoSeguimientoService
    ) {}

    public function index(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));

        $pedidos = Pedido::query()
            ->visibleEnAdmin()
            ->with([
                'usuario',
                'estatus',
                'pago.metodoPago',
                'pago.estatus',
            ])
            ->buscarAdmin($terminoBusqueda)
            ->orderBy('created_at')
            ->orderBy('Id_Pedido')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pedidos.index', compact('pedidos', 'terminoBusqueda'));
    }

    public function show(Pedido $pedido): View
    {
        $pedido->load([
            'usuario',
            'estatus',
            'pago.metodoPago',
            'pago.estatus',
            'direccion.municipio.departamento',
            'envio.estatus',
            'detalle.producto.imagenes',
            'boletaPago',
        ]);

        $puedeCancelar = $this->adminPedidoService->puedeCancelar($pedido);

        return view('admin.pedidos.show', compact('pedido', 'puedeCancelar'));
    }

    public function historialIndex(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));

        $eventos = PedidoHistorial::query()
            ->with([
                'estatus',
                'pedido.usuario',
                'pedido.estatus',
            ])
            ->whereHas('pedido', fn ($query) => $query->visibleEnAdmin())
            ->buscarAdmin($terminoBusqueda)
            ->orderBy('Fecha_Cambio')
            ->orderBy('Id_PedidoHistorial')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pedidos.historial-index', compact('eventos', 'terminoBusqueda'));
    }

    public function historial(Pedido $pedido): View
    {
        $pedido->load([
            'usuario',
            'estatus',
            'historial.estatus',
        ]);

        $eventos = $pedido->historial
            ->sortBy('Fecha_Cambio')
            ->values();

        return view('admin.pedidos.historial', compact('pedido', 'eventos'));
    }

    public function seguimientoIndex(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));
        $filtroAccion = $this->adminPedidoSeguimientoService->accionDesdeFiltro(
            (string) $request->input('accion', '')
        );

        $estatusIds = $this->adminPedidoSeguimientoService->estatusPendientesSeguimiento();

        if ($filtroAccion !== null) {
            $estatusFiltrado = $this->adminPedidoSeguimientoService->estatusIdParaAccion($filtroAccion);

            if ($estatusFiltrado !== null) {
                $estatusIds = [$estatusFiltrado];
            }
        }

        $pedidos = Pedido::query()
            ->pendientesSeguimientoAdmin($estatusIds)
            ->with([
                'usuario',
                'estatus',
                'pago.metodoPago',
                'pago.estatus',
            ])
            ->buscarAdmin($terminoBusqueda)
            ->orderBy('created_at')
            ->orderBy('Id_Pedido')
            ->paginate(15)
            ->withQueryString();

        $pedidos->getCollection()->transform(function (Pedido $pedido) {
            $pedido->setAttribute(
                'accion_seguimiento',
                $this->adminPedidoSeguimientoService->accionDisponible($pedido)
            );
            $pedido->setAttribute(
                'bloqueo_seguimiento',
                $this->adminPedidoSeguimientoService->motivoBloqueoAccion($pedido)
            );

            return $pedido;
        });

        $conteosAccion = [
            AdminPedidoSeguimientoService::ACCION_CONFIRMAR => Pedido::query()
                ->pendientesSeguimientoAdmin([EstatusCatalog::PEDIDO_PENDIENTE])
                ->count(),
            AdminPedidoSeguimientoService::ACCION_PREPARACION => Pedido::query()
                ->pendientesSeguimientoAdmin([EstatusCatalog::PEDIDO_CONFIRMADO])
                ->count(),
            AdminPedidoSeguimientoService::ACCION_ENVIADO => Pedido::query()
                ->pendientesSeguimientoAdmin([EstatusCatalog::PEDIDO_EN_PREPARACION])
                ->count(),
            AdminPedidoSeguimientoService::ACCION_ENTREGADO => Pedido::query()
                ->pendientesSeguimientoAdmin([EstatusCatalog::PEDIDO_ENVIADO])
                ->count(),
        ];

        $totalPendientes = array_sum($conteosAccion);

        return view('admin.pedidos.seguimiento-index', compact(
            'pedidos',
            'terminoBusqueda',
            'filtroAccion',
            'conteosAccion',
            'totalPendientes',
        ));
    }

    public function seguimiento(Pedido $pedido): View
    {
        $pedido->load([
            'usuario',
            'estatus',
            'pago.metodoPago',
            'pago.estatus',
            'envio.estatus',
            'historial.estatus',
            'boletaPago',
        ]);

        $accionDisponible = $this->adminPedidoSeguimientoService->accionDisponible($pedido);

        $eventos = $pedido->historial
            ->sortBy('Fecha_Cambio')
            ->values();

        $puedeCancelar = $this->adminPedidoService->puedeCancelar($pedido);

        return view('admin.pedidos.seguimiento', compact(
            'pedido',
            'accionDisponible',
            'eventos',
            'puedeCancelar',
        ));
    }

    public function seguimientoConfirmar(Request $request, Pedido $pedido): RedirectResponse
    {
        $request->validate([
            'comentario' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->adminPedidoSeguimientoService->confirmar(
                $pedido,
                $request->input('comentario')
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.pedidos.seguimiento', $pedido)
                ->withErrors($e->errors(), 'seguimiento');
        }

        return redirect()
            ->route('admin.pedidos.seguimiento', $pedido)
            ->with('success', 'Pedido confirmado. El usuario verá el cambio en Seguimiento.');
    }

    public function seguimientoPreparacion(Request $request, Pedido $pedido): RedirectResponse
    {
        $request->validate([
            'comentario' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->adminPedidoSeguimientoService->enPreparacion(
                $pedido,
                $request->input('comentario')
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.pedidos.seguimiento', $pedido)
                ->withErrors($e->errors(), 'seguimiento');
        }

        return redirect()
            ->route('admin.pedidos.seguimiento', $pedido)
            ->with('success', 'Pedido marcado en preparación.');
    }

    public function seguimientoEnviado(AdminPedidoSeguimientoEnviadoRequest $request, Pedido $pedido): RedirectResponse
    {
        try {
            $this->adminPedidoSeguimientoService->marcarEnviado(
                $pedido,
                (string) $request->validated('empresa_envio'),
                (string) $request->validated('numero_guia'),
                $request->validated('comentario')
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.pedidos.seguimiento', $pedido)
                ->withErrors($e->errors(), 'seguimiento')
                ->withInput();
        }

        return redirect()
            ->route('admin.pedidos.seguimiento', $pedido)
            ->with('success', 'Pedido marcado como enviado. Se notificó al usuario por correo y WhatsApp (si está configurado).');
    }

    public function seguimientoEntregado(Request $request, Pedido $pedido): RedirectResponse
    {
        $request->validate([
            'comentario' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->adminPedidoSeguimientoService->marcarEntregado(
                $pedido,
                $request->input('comentario')
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.pedidos.seguimiento', $pedido)
                ->withErrors($e->errors(), 'seguimiento');
        }

        return redirect()
            ->route('admin.pedidos.seguimiento', $pedido)
            ->with('success', 'Pedido marcado como entregado.');
    }

    public function cancelar(AdminPedidoCancelarRequest $request, Pedido $pedido): RedirectResponse
    {
        try {
            $resultado = $this->adminPedidoService->cancelar(
                $pedido,
                $request->input('comentario')
            );
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors(), 'pedido');
        }

        $mensaje = 'Pedido cancelado.';

        if ($resultado['repuso_stock']) {
            $mensaje .= ' Se repuso el stock en inventario.';
        }

        return redirect()
            ->back()
            ->with('success', $mensaje);
    }

    public function destroy(Pedido $pedido): RedirectResponse
    {
        try {
            $this->adminPedidoService->ocultar($pedido);
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.pedidos.index')
                ->withErrors($e->errors());
        }

        return redirect()
            ->back()
            ->with('success', 'Pedido ocultado del listado administrativo.');
    }
}
