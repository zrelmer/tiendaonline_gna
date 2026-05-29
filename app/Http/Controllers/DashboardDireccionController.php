<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardDireccionStoreRequest;
use App\Http\Requests\DashboardDireccionUpdateRequest;
use App\Models\Direccion;
use App\Services\DireccionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DashboardDireccionController extends Controller
{
    public function __construct(
        protected DireccionService $direccionService
    ) {}

    public function store(DashboardDireccionStoreRequest $request): RedirectResponse
    {
        try {
            $this->direccionService->crearParaUsuario(
                $request->user(),
                $request->validated('direccion'),
                (int) $request->validated('id_departamento'),
                (int) $request->validated('id_municipio'),
            );
        } catch (ValidationException $e) {
            return $this->redirectConErrores($e);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Dirección registrada correctamente.')
            ->with('tab', 'addresses');
    }

    public function update(DashboardDireccionUpdateRequest $request, Direccion $direccion): RedirectResponse
    {
        try {
            $this->direccionService->actualizar(
                $direccion,
                $request->validated('direccion'),
                (int) $request->validated('id_departamento'),
                (int) $request->validated('id_municipio'),
            );
        } catch (ValidationException $e) {
            return $this->redirectConErrores($e, (int) $direccion->Id_Direccion);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Dirección actualizada correctamente.')
            ->with('tab', 'addresses');
    }

    public function destroy(Request $request, Direccion $direccion): RedirectResponse
    {
        if ((int) $direccion->Id_Usuario !== (int) $request->user()->Id_Usuario) {
            abort(403);
        }

        try {
            $this->direccionService->eliminar($direccion);
        } catch (ValidationException $e) {
            return redirect()
                ->route('dashboard')
                ->withErrors($e->errors())
                ->with('tab', 'addresses');
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Dirección eliminada correctamente.')
            ->with('tab', 'addresses');
    }

    protected function redirectConErrores(ValidationException $e, ?int $idDireccionEditar = null): RedirectResponse
    {
        $redirect = redirect()
            ->route('dashboard')
            ->withErrors($e->errors(), 'direccion')
            ->withInput()
            ->with('tab', 'addresses')
            ->with('abrir_modal_direccion', $idDireccionEditar ? 'editar' : 'nueva');

        if ($idDireccionEditar) {
            $redirect->with('editar_direccion_id', $idDireccionEditar);
        }

        return $redirect;
    }
}
