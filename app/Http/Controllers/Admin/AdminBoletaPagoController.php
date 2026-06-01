<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoletaPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminBoletaPagoController extends Controller
{
    public function index(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));

        $boletas = BoletaPago::query()
            ->with(['pedido.usuario'])
            ->buscarAdmin($terminoBusqueda)
            ->orderByDesc('created_at')
            ->orderByDesc('Id_Boletapago')
            ->paginate(15)
            ->withQueryString();

        return view('admin.boletas.index', compact('boletas', 'terminoBusqueda'));
    }

    public function show(BoletaPago $boleta): View
    {
        $boleta->load(['pedido.usuario']);

        return view('admin.boletas.show', compact('boleta'));
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
