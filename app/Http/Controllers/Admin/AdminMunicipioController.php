<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminMunicipioStoreRequest;
use App\Http\Requests\AdminMunicipioUpdateRequest;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Services\AdminMunicipioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminMunicipioController extends Controller
{
    public function __construct(
        protected AdminMunicipioService $adminMunicipioService
    ) {}

    public function index(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));

        $municipios = Municipio::query()
            ->with('departamento')
            ->withCount('direcciones')
            ->buscarAdmin($terminoBusqueda)
            ->join('tb_departamento', 'tb_municipio.Id_Departamento', '=', 'tb_departamento.Id_Departamento')
            ->orderBy('tb_departamento.Nom_Departamento')
            ->orderBy('tb_municipio.Nom_Municipio')
            ->select('tb_municipio.*')
            ->paginate(15)
            ->withQueryString();

        return view('admin.municipios.index', compact('municipios', 'terminoBusqueda'));
    }

    public function create(): View
    {
        return view('admin.municipios.add', [
            'departamentos' => $this->departamentosParaSelect(),
        ]);
    }

    public function store(AdminMunicipioStoreRequest $request): RedirectResponse
    {
        $this->adminMunicipioService->crear($request->validated());

        return redirect()
            ->route('admin.municipios.index')
            ->with('success', 'Municipio creado correctamente.');
    }

    public function edit(Municipio $municipio): View
    {
        $municipio->loadCount('direcciones');

        return view('admin.municipios.edit', [
            'municipio' => $municipio,
            'departamentos' => $this->departamentosParaSelect(),
        ]);
    }

    public function update(AdminMunicipioUpdateRequest $request, Municipio $municipio): RedirectResponse
    {
        $this->adminMunicipioService->actualizar($municipio, $request->validated());

        return redirect()
            ->route('admin.municipios.edit', $municipio)
            ->with('success', 'Municipio actualizado correctamente.');
    }

    public function destroy(Municipio $municipio): RedirectResponse
    {
        try {
            $this->adminMunicipioService->eliminar($municipio);
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.municipios.index')
                ->withErrors($e->errors());
        }

        return redirect()
            ->route('admin.municipios.index')
            ->with('success', 'Municipio eliminado correctamente.');
    }

    /**
     * @return Collection<int, Departamento>
     */
    private function departamentosParaSelect(): Collection
    {
        return Departamento::query()
            ->orderBy('Nom_Departamento')
            ->get();
    }
}
