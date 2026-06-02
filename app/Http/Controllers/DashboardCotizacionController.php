<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardCotizacionAceptarRequest;
use App\Http\Requests\DashboardCotizacionRechazarRequest;
use App\Http\Requests\DashboardCotizacionStoreRequest;
use App\Models\Cotizacion;
use App\Services\CotizacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardCotizacionController extends Controller
{
    public function __construct(
        protected CotizacionService $cotizacionService
    ) {}

    public function store(DashboardCotizacionStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->cotizacionService->crearSolicitud([
                'nombre_cliente' => $validated['nombre_cliente'],
                'nit' => $validated['nit'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'email' => $validated['email'] ?? null,
                'notas' => $validated['notas'] ?? null,
                'items' => $validated['items'],
            ]);
        } catch (ValidationException $e) {
            return redirect()
                ->route('dashboard')
                ->withErrors($e->errors(), 'cotizacion')
                ->withInput()
                ->with('tab', 'quotes')
                ->with('abrir_formulario_cotizacion', true);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Tu solicitud de cotización fue enviada. Te notificaremos cuando esté lista.')
            ->with('tab', 'quotes');
    }

    public function aceptar(DashboardCotizacionAceptarRequest $request, Cotizacion $cotizacion): RedirectResponse
    {
        if ((int) $cotizacion->Id_Usuario !== (int) $request->user()->Id_Usuario) {
            abort(403);
        }

        try {
            $this->cotizacionService->aceptar(
                $cotizacion,
                $request->input('comentario'),
                (int) $request->user()->Id_Usuario
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('dashboard')
                ->withErrors($e->errors(), 'cotizacion')
                ->with('tab', 'quotes');
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Cotización aceptada correctamente. Gracias por confirmar.')
            ->with('tab', 'quotes');
    }

    public function rechazar(DashboardCotizacionRechazarRequest $request, Cotizacion $cotizacion): RedirectResponse
    {
        if ((int) $cotizacion->Id_Usuario !== (int) $request->user()->Id_Usuario) {
            abort(403);
        }

        try {
            $this->cotizacionService->rechazar(
                $cotizacion,
                $request->input('comentario'),
                (int) $request->user()->Id_Usuario
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('dashboard')
                ->withErrors($e->errors(), 'cotizacion')
                ->withInput()
                ->with('tab', 'quotes');
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Cotización rechazada. Puedes solicitar una nueva cuando lo necesites.')
            ->with('tab', 'quotes');
    }

    public function download(Request $request, Cotizacion $cotizacion): StreamedResponse
    {
        if ((int) $cotizacion->Id_Usuario !== (int) $request->user()->Id_Usuario) {
            abort(403);
        }

        if (! $cotizacion->puedeDescargarArchivo()) {
            abort(404, 'El archivo de cotización no está disponible.');
        }

        return Storage::disk('public')->download(
            $cotizacion->Cot_Archivo,
            $cotizacion->nombreArchivoDescarga()
        );
    }
}
