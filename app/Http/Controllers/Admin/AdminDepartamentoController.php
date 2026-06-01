<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminDepartamentoStoreRequest;
use App\Http\Requests\AdminDepartamentoUpdateRequest;
use App\Models\Departamento;
use App\Services\AdminDepartamentoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminDepartamentoController extends Controller
{
    public function __construct(
        protected AdminDepartamentoService $adminDepartamentoService
    ) {}

    public function index(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));

        $departamentos = Departamento::query()
            ->withCount('municipios')
            ->buscarAdmin($terminoBusqueda)
            ->orderBy('Nom_Departamento')
            ->paginate(15)
            ->withQueryString();

        return view('admin.departamentos.index', compact('departamentos', 'terminoBusqueda'));
    }

    public function create(): View
    {
        return view('admin.departamentos.add');
    }

    public function store(AdminDepartamentoStoreRequest $request): RedirectResponse
    {
        $this->adminDepartamentoService->crear($request->validated());

        return redirect()
            ->route('admin.departamentos.index')
            ->with('success', 'Departamento creado correctamente.');
    }

    public function edit(Departamento $departamento): View
    {
        $departamento->loadCount('municipios');

        return view('admin.departamentos.edit', compact('departamento'));
    }

    public function update(AdminDepartamentoUpdateRequest $request, Departamento $departamento): RedirectResponse
    {
        $this->adminDepartamentoService->actualizar($departamento, $request->validated());

        return redirect()
            ->route('admin.departamentos.edit', $departamento)
            ->with('success', 'Departamento actualizado correctamente.');
    }

    public function destroy(Departamento $departamento): RedirectResponse
    {
        try {
            $this->adminDepartamentoService->eliminar($departamento);
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.departamentos.index')
                ->withErrors($e->errors());
        }

        return redirect()
            ->route('admin.departamentos.index')
            ->with('success', 'Departamento eliminado correctamente.');
    }
}
