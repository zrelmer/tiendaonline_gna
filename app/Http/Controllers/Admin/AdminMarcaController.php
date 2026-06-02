<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminMarcaStoreRequest;
use App\Http\Requests\AdminMarcaUpdateRequest;
use App\Models\Marca;
use App\Services\AdminMarcaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminMarcaController extends Controller
{
    public function __construct(
        protected AdminMarcaService $adminMarcaService
    ) {}

    public function index(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));

        $marcas = Marca::query()
            ->withCount('productos')
            ->buscarAdmin($terminoBusqueda)
            ->orderBy('Nom_Marca')
            ->paginate(15)
            ->withQueryString();

        return view('admin.marcas.index', compact('marcas', 'terminoBusqueda'));
    }

    public function create(): View
    {
        return view('admin.marcas.add');
    }

    public function store(AdminMarcaStoreRequest $request): RedirectResponse
    {
        $this->adminMarcaService->crear($request->validated());

        return redirect()
            ->route('admin.marcas.index')
            ->with('success', 'Marca creada correctamente.');
    }

    public function edit(Marca $marca): View
    {
        $marca->loadCount('productos');

        return view('admin.marcas.edit', compact('marca'));
    }

    public function update(AdminMarcaUpdateRequest $request, Marca $marca): RedirectResponse
    {
        $this->adminMarcaService->actualizar($marca, $request->validated());

        return redirect()
            ->route('admin.marcas.edit', $marca)
            ->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(Marca $marca): RedirectResponse
    {
        try {
            $this->adminMarcaService->eliminar($marca);
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.marcas.index')
                ->withErrors($e->errors());
        }

        return redirect()
            ->route('admin.marcas.index')
            ->with('success', 'Marca eliminada correctamente.');
    }
}
