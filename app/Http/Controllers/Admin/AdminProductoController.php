<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminProductoStoreRequest;
use App\Http\Requests\AdminProductoUpdateRequest;
use App\Models\Categoria;
use App\Models\Estatus;
use App\Models\Marca;
use App\Models\Producto;
use App\Services\AdminProductoExportService;
use App\Services\AdminProductoService;
use App\Support\EstatusCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminProductoController extends Controller
{
    public function __construct(
        protected AdminProductoService $adminProductoService,
        protected AdminProductoExportService $adminProductoExportService
    ) {}

    public function index(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));

        $productos = Producto::query()
            ->with(['categoria', 'marca', 'inventario', 'imagenes', 'estatus'])
            ->buscarAdmin($terminoBusqueda)
            ->orderByDesc('Id_Producto')
            ->paginate(15)
            ->withQueryString();

        return view('admin.productos.index', compact('productos', 'terminoBusqueda'));
    }

    public function create(): View
    {
        return view('admin.productos.add', [
            'categorias' => Categoria::query()->orderBy('Cate_Nombre')->get(),
            'marcas' => Marca::query()->orderBy('Nom_Marca')->get(),
            'estatusProducto' => Estatus::query()
                ->whereIn('Id_Estatus', [
                    EstatusCatalog::PRODUCTO_ACTIVO,
                    EstatusCatalog::PRODUCTO_INACTIVO,
                    EstatusCatalog::PRODUCTO_AGOTADO,
                    EstatusCatalog::PRODUCTO_PENDIENTE,
                ])
                ->orderBy('Id_Estatus')
                ->get(),
        ]);
    }

    public function store(AdminProductoStoreRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $imagenes = $request->file('imagenes', []);

        $this->adminProductoService->crear($datos, is_array($imagenes) ? $imagenes : []);

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto): View
    {
        $producto->load(['inventario', 'imagenes']);

        return view('admin.productos.edit', [
            'producto' => $producto,
            'categorias' => Categoria::query()->orderBy('Cate_Nombre')->get(),
            'marcas' => Marca::query()->orderBy('Nom_Marca')->get(),
            'estatusProducto' => Estatus::query()
                ->whereIn('Id_Estatus', [
                    EstatusCatalog::PRODUCTO_ACTIVO,
                    EstatusCatalog::PRODUCTO_INACTIVO,
                    EstatusCatalog::PRODUCTO_AGOTADO,
                    EstatusCatalog::PRODUCTO_PENDIENTE,
                ])
                ->orderBy('Id_Estatus')
                ->get(),
        ]);
    }

    public function update(AdminProductoUpdateRequest $request, Producto $producto): RedirectResponse
    {
        $datos = $request->validated();
        $imagenes = $request->file('imagenes', []);

        $this->adminProductoService->actualizar($producto, $datos, is_array($imagenes) ? $imagenes : []);

        return redirect()
            ->route('admin.productos.edit', $producto)
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        try {
            $this->adminProductoService->eliminar($producto);
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.productos.index')
                ->withErrors($e->errors());
        }

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    public function export(Request $request, string $format): StreamedResponse
    {
        if ($format !== 'csv') {
            abort(404);
        }

        $terminoBusqueda = trim((string) $request->input('q', ''));

        return $this->adminProductoExportService->descargarCsv($terminoBusqueda);
    }
}
