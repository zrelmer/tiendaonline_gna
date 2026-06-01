<?php

namespace App\Http\Controllers;

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

    public function download(Request $request, Cotizacion $cotizacion): StreamedResponse
    {
        if ((int) $cotizacion->Id_Usuario !== (int) $request->user()->Id_Usuario) {
            abort(403);
        }

        if (! $cotizacion->Cot_Archivo || ! Storage::disk('public')->exists($cotizacion->Cot_Archivo)) {
            abort(404, 'El archivo de cotización aún no está disponible.');
        }

        return Storage::disk('public')->download(
            $cotizacion->Cot_Archivo,
            basename($cotizacion->Cot_Archivo)
        );
    }
}
