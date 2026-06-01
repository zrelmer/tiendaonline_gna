<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminCategoriaStoreRequest;
use App\Http\Requests\AdminCategoriaUpdateRequest;
use App\Models\Categoria;
use App\Services\AdminCategoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminCategoriaController extends Controller
{
    public function __construct(
        protected AdminCategoriaService $adminCategoriaService
    ) {}

    public function index(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));

        $categorias = Categoria::query()
            ->buscarAdmin($terminoBusqueda)
            ->orderByDesc('Id_Categoria')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categorias.index', compact('categorias', 'terminoBusqueda'));
    }

    public function create(): View
    {
        return view('admin.categorias.add');
    }

    public function store(AdminCategoriaStoreRequest $request): RedirectResponse
    {
        $this->adminCategoriaService->crear(
            $request->validated(),
            $request->file('imagen')
        );

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Categoria $categoria): View
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(AdminCategoriaUpdateRequest $request, Categoria $categoria): RedirectResponse
    {
        $this->adminCategoriaService->actualizar(
            $categoria,
            $request->validated(),
            $request->file('imagen')
        );

        return redirect()
            ->route('admin.categorias.edit', $categoria)
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria): RedirectResponse
    {
        try {
            $this->adminCategoriaService->eliminar($categoria);
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.categorias.index')
                ->withErrors($e->errors());
        }

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
