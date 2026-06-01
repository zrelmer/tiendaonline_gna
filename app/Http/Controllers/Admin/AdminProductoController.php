<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class AdminProductoController extends Controller
{
    /**
     * Listado de productos (estructura de vista; datos y acciones se completan después).
     */
    public function index(Request $request): View
    {
        return view('admin.productos.index', [
            // Descomenta cuando conectes datos reales:
            // 'productos' => Producto::query()
            //     ->with(['categoria', 'marca'])
            //     ->when($request->filled('q'), fn ($q) => $q->where('Prod_Nombre', 'like', '%'.$request->q.'%'))
            //     ->orderByDesc('Id_Producto')
            //     ->paginate(15),
            'productos' => collect(),
        ]);
    }

    /** @todo Implementar formulario de edición */
    public function edit(Producto $producto): View
    {
        return view('admin.productos.edit', compact('producto'));
    }

    /** @todo Implementar eliminación */
    public function destroy(Producto $producto): Response
    {
        abort(501, 'Eliminar producto: pendiente de implementar.');
    }

    /** @todo Implementar exportación Excel/CSV */
    public function export(Request $request, string $format): Response
    {
        abort(501, 'Exportar productos: pendiente de implementar.');
    }
}
