<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;


class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Usamos la columna real del modelo personalizado.
        $status = Password::sendResetLink([
            'Usu_Correo' => $request->email,
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Revisa tu correo para recuperar tu contraseña.')
            : back()->withErrors(['email' => __($status)]);
    }
}