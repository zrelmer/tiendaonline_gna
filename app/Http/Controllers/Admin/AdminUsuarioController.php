<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUsuarioUpdateRequest;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\AdminUsuarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminUsuarioController extends Controller
{
    public function __construct(
        protected AdminUsuarioService $adminUsuarioService
    ) {}

    public function index(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));

        $usuarios = Usuario::query()
            ->with([
                'roles',
                'direcciones.municipio.departamento',
            ])
            ->buscarAdmin($terminoBusqueda)
            ->orderBy('Usu_Nombre')
            ->paginate(15)
            ->withQueryString();

        return view('admin.usuarios.index', compact('usuarios', 'terminoBusqueda'));
    }

    public function edit(Usuario $usuario): View
    {
        $usuario->load([
            'roles',
            'direcciones.municipio.departamento',
        ]);

        return view('admin.usuarios.edit', [
            'usuario' => $usuario,
            'roles' => $this->rolesParaSelect(),
        ]);
    }

    public function update(AdminUsuarioUpdateRequest $request, Usuario $usuario): RedirectResponse
    {
        try {
            $this->adminUsuarioService->actualizarRol(
                $usuario,
                (int) $request->validated('Id_Rol')
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.usuarios.edit', $usuario)
                ->withErrors($e->errors())
                ->withInput();
        }

        return redirect()
            ->route('admin.usuarios.edit', $usuario)
            ->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * @return Collection<int, Rol>
     */
    private function rolesParaSelect(): Collection
    {
        return Rol::query()
            ->orderBy('Rol_Nombre')
            ->get();
    }
}
