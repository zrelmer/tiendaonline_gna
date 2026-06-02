<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminBoletaPagoAprobarRequest;
use App\Http\Requests\AdminBoletaPagoRechazarRequest;
use App\Models\BoletaPago;
use App\Services\AdminBoletaPagoService;
use App\Services\PedidoService;
use App\Support\EstatusCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminBoletaPagoController extends Controller
{
    public function __construct(
        protected AdminBoletaPagoService $adminBoletaPagoService
    ) {}

    public function index(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));
        $filtroEstado = trim((string) $request->input('estado', ''));

        if (! in_array($filtroEstado, ['', 'pendiente', 'procesada'], true)) {
            $filtroEstado = '';
        }

        $query = BoletaPago::query()
            ->with([
                'pedido.usuario',
                'pedido.estatus',
                'pedido.pago.estatus',
            ])
            ->buscarAdmin($terminoBusqueda);

        if ($filtroEstado === 'pendiente') {
            $query->pendientesVerificacionAdmin()
                ->orderBy('created_at')
                ->orderBy('Id_Boletapago');
        } else {
            if ($filtroEstado === 'procesada') {
                $query->whereDoesntHave('pedido', function ($pedido) {
                    $pedido
                        ->visibleEnAdmin()
                        ->where('Id_Estatus', EstatusCatalog::PEDIDO_PENDIENTE)
                        ->whereHas('pago', function ($pago) {
                            $pago
                                ->where('Id_MetodoPago', PedidoService::METODO_TRANSFERENCIA)
                                ->where('Id_Estatus', EstatusCatalog::PAGO_PENDIENTE_VERIFICACION);
                        });
                });
            }

            $query
                ->orderByDesc('created_at')
                ->orderByDesc('Id_Boletapago');
        }

        $boletas = $query
            ->paginate(15)
            ->withQueryString();

        $boletas->getCollection()->transform(function (BoletaPago $boleta) {
            $boleta->setAttribute(
                'puede_aprobar',
                $this->adminBoletaPagoService->puedeAprobar($boleta)
            );

            return $boleta;
        });

        $conteoPendientes = $this->adminBoletaPagoService->contarPendientesVerificacion();

        return view('admin.boletas.index', compact(
            'boletas',
            'terminoBusqueda',
            'filtroEstado',
            'conteoPendientes',
        ));
    }

    public function show(BoletaPago $boleta): View
    {
        $boleta->load([
            'pedido.usuario',
            'pedido.estatus',
            'pedido.pago.estatus',
            'pedido.pago.metodoPago',
        ]);

        $puedeAprobar = $this->adminBoletaPagoService->puedeAprobar($boleta);
        $puedeRechazar = $this->adminBoletaPagoService->puedeRechazar($boleta);
        $motivoNoAprobable = $this->adminBoletaPagoService->motivoNoAprobable($boleta);

        return view('admin.boletas.show', compact(
            'boleta',
            'puedeAprobar',
            'puedeRechazar',
            'motivoNoAprobable',
        ));
    }

    public function aprobar(AdminBoletaPagoAprobarRequest $request, BoletaPago $boleta): RedirectResponse
    {
        try {
            $this->adminBoletaPagoService->aprobar(
                $boleta,
                $request->input('comentario')
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.boletas.show', $boleta)
                ->withErrors($e->errors(), 'boleta');
        }

        return redirect()
            ->route('admin.boletas.show', $boleta)
            ->with('success', 'Comprobante aprobado. El pago quedó como Pagado y el pedido pasó a Confirmado. El usuario lo verá en Seguimiento.');
    }

    public function rechazar(AdminBoletaPagoRechazarRequest $request, BoletaPago $boleta): RedirectResponse
    {
        try {
            $pedido = $this->adminBoletaPagoService->rechazar(
                $boleta,
                (string) $request->input('motivo')
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.boletas.show', $boleta)
                ->withErrors($e->errors(), 'boleta');
        }

        return redirect()
            ->route('admin.pedidos.show', $pedido)
            ->with('success', 'Comprobante rechazado. El cliente puede subir un nuevo comprobante desde su panel.');
    }

    public function download(BoletaPago $boleta): StreamedResponse
    {
        $boleta->load('pedido');

        if (! $boleta->archivoDisponible()) {
            abort(404, 'El comprobante no está disponible.');
        }

        return Storage::disk('public')->download(
            $boleta->BoletaImagen,
            $boleta->nombreArchivoDescarga()
        );
    }
}
