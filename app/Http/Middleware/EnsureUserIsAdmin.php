<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (! $usuario instanceof Usuario || ! $usuario->esAdministrador()) {
            abort(403, 'No tienes permiso para acceder al panel de administración.');
        }

        return $next($request);
    }
}
