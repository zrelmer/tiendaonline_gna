<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminUsuarioService
{
    public function actualizarRol(Usuario $usuario, int $idRol): Usuario
    {
        $usuarioAutenticado = Auth::user();

        if ($usuarioAutenticado instanceof Usuario
            && (int) $usuario->Id_Usuario === (int) $usuarioAutenticado->Id_Usuario
            && $idRol !== Usuario::ROL_ADMIN) {
            throw ValidationException::withMessages([
                'Id_Rol' => 'No puedes quitarte tu propio rol de administrador.',
            ]);
        }

        if ($usuario->esAdministrador() && $idRol !== Usuario::ROL_ADMIN) {
            $administradoresRestantes = Usuario::query()
                ->where('Id_Rol', Usuario::ROL_ADMIN)
                ->where('Id_Usuario', '!=', $usuario->Id_Usuario)
                ->exists();

            if (! $administradoresRestantes) {
                throw ValidationException::withMessages([
                    'Id_Rol' => 'No se puede cambiar el rol: debe existir al menos un administrador.',
                ]);
            }
        }

        $usuario->update([
            'Id_Rol' => $idRol,
        ]);

        return $usuario->fresh(['roles']);
    }
}
