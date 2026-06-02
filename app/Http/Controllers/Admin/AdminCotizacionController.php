<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminCotizacionEmitirRequest;
use App\Http\Requests\AdminCotizacionRevisionRequest;
use App\Models\Cotizacion;
use App\Services\AdminCotizacionService;
use App\Services\CotizacionService;
use App\Support\EstatusCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminCotizacionController extends Controller
{
    public function __construct(
        protected AdminCotizacionService $adminCotizacionService,
        protected CotizacionService $cotizacionService
    ) {}

    public function index(Request $request): View
    {
        $this->cotizacionService->sincronizarVencidas();

        $terminoBusqueda = trim((string) $request->input('q', ''));

        $cotizaciones = Cotizacion::query()
            ->with(['usuario', 'estatus'])
            ->buscarAdmin($terminoBusqueda)
            ->orderBy('created_at')
            ->orderBy('Id_Cotizacion')
            ->paginate(15)
            ->withQueryString();

        return view('admin.cotizaciones.index', compact('cotizaciones', 'terminoBusqueda'));
    }

    public function pendientesIndex(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));
        $filtroAccion = $this->adminCotizacionService->accionDesdeFiltro(
            (string) $request->input('accion', '')
        );

        $estatusIds = $this->adminCotizacionService->estatusPendientesAtencion();

        if ($filtroAccion !== null) {
            $estatusFiltrado = $this->adminCotizacionService->estatusIdParaAccion($filtroAccion);

            if ($estatusFiltrado !== null) {
                $estatusIds = [$estatusFiltrado];
            }
        }

        $cotizaciones = Cotizacion::query()
            ->pendientesAdmin($estatusIds)
            ->with(['usuario', 'estatus'])
            ->buscarAdmin($terminoBusqueda)
            ->orderBy('created_at')
            ->orderBy('Id_Cotizacion')
            ->paginate(15)
            ->withQueryString();

        $cotizaciones->getCollection()->transform(function (Cotizacion $cotizacion) {
            $cotizacion->setAttribute(
                'accion_cotizacion',
                $this->adminCotizacionService->accionDisponible($cotizacion)
            );

            return $cotizacion;
        });

        $conteosAccion = [
            AdminCotizacionService::ACCION_REVISION => Cotizacion::query()
                ->pendientesAdmin([EstatusCatalog::COTIZACION_SOLICITUD_RECIBIDA])
                ->count(),
            AdminCotizacionService::ACCION_EN_REVISION => Cotizacion::query()
                ->pendientesAdmin([EstatusCatalog::COTIZACION_EN_REVISION])
                ->count(),
        ];

        $totalPendientes = array_sum($conteosAccion);

        return view('admin.cotizaciones.pendientes-index', compact(
            'cotizaciones',
            'terminoBusqueda',
            'filtroAccion',
            'conteosAccion',
            'totalPendientes',
        ));
    }

    public function show(Cotizacion $cotizacion): View
    {
        $this->cotizacionService->sincronizarVencidas((int) $cotizacion->Id_Usuario);
        $cotizacion->refresh();

        $cotizacion->load([
            'usuario',
            'estatus',
            'detalle.producto',
            'historial.estatus',
        ]);

        $eventos = $cotizacion->historial
            ->sortBy('Fecha_Cambio')
            ->values();

        $accionDisponible = $this->adminCotizacionService->accionDisponible($cotizacion);
        $puedeMarcarEnRevision = $this->adminCotizacionService->puedeMarcarEnRevision($cotizacion);

        return view('admin.cotizaciones.show', compact(
            'cotizacion',
            'eventos',
            'accionDisponible',
            'puedeMarcarEnRevision',
        ));
    }

    public function marcarEnRevision(AdminCotizacionRevisionRequest $request, Cotizacion $cotizacion): RedirectResponse
    {
        try {
            $this->adminCotizacionService->marcarEnRevision(
                $cotizacion,
                $request->input('comentario')
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.cotizaciones.show', $cotizacion)
                ->withErrors($e->errors(), 'cotizacion');
        }

        return redirect()
            ->route('admin.cotizaciones.show', $cotizacion)
            ->with('success', 'Solicitud marcada en revisión. El usuario lo verá en su panel de cotizaciones.');
    }

    public function emitir(Cotizacion $cotizacion): View|RedirectResponse
    {
        $motivo = $this->adminCotizacionService->motivoNoEmitir($cotizacion);

        if ($motivo !== null) {
            return redirect()
                ->route('admin.cotizaciones.show', $cotizacion)
                ->withErrors(['cotizacion' => $motivo], 'cotizacion');
        }

        $cotizacion->load(['usuario', 'estatus', 'detalle.producto']);

        $terminosDefault = (string) config('cotizacion.terminos_default', '');
        $vigenciaDefault = (int) config('cotizacion.vigencia_dias', 10);

        return view('admin.cotizaciones.emitir', compact(
            'cotizacion',
            'terminosDefault',
            'vigenciaDefault',
        ));
    }

    public function emitirStore(AdminCotizacionEmitirRequest $request, Cotizacion $cotizacion): RedirectResponse
    {
        try {
            $this->adminCotizacionService->emitir(
                $cotizacion,
                $request->input('lineas', []),
                (string) $request->input('terminos'),
                (int) $request->input('vigencia_dias'),
                $request->file('archivo'),
                $request->input('comentario')
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.cotizaciones.emitir', $cotizacion)
                ->withErrors($e->errors(), 'cotizacion')
                ->withInput();
        }

        return redirect()
            ->route('admin.cotizaciones.show', $cotizacion)
            ->with('success', 'Cotización emitida. El cliente puede ver los términos y descargar el PDF desde su panel.');
    }

    public function download(Cotizacion $cotizacion): StreamedResponse
    {
        if (! $cotizacion->archivoDisponible()) {
            abort(404, 'El archivo de cotización no está disponible.');
        }

        return Storage::disk('public')->download(
            $cotizacion->Cot_Archivo,
            $cotizacion->nombreArchivoDescarga()
        );
    }
}
