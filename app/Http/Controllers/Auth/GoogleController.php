<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\BienvenidaUsuario;
use App\Models\Carrito;
use App\Models\Usuario;
use App\Services\AdminNotificationService;
use App\Services\WhatsAppService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            Log::warning('Google OAuth failed', ['message' => $e->getMessage()]);

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'No se pudo iniciar sesión con Google. Intenta de nuevo.']);
        }

        $email = $googleUser->getEmail();
        if (! $email) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Google no proporcionó un correo electrónico.']);
        }

        $usuario = Usuario::query()
            ->where('google_id', $googleUser->getId())
            ->first();

        if (! $usuario) {
            $usuario = Usuario::query()
                ->where('Usu_Correo', $email)
                ->first();
        }

        $isNew = false;

        if ($usuario) {
            if (! $usuario->google_id) {
                $usuario->update(['google_id' => $googleUser->getId()]);
            }
        } else {
            $isNew = true;
            $usuario = Usuario::create([
                'Usu_Nombre' => $googleUser->getName() ?: strstr($email, '@', true),
                'Usu_Correo' => $email,
                'Usu_Pass' => null,
                'Usu_Telefono' => 'Pendiente',
                'google_id' => $googleUser->getId(),
                'Id_Rol' => Usuario::ROL_USUARIO,
            ]);

            Carrito::firstOrCreate(['Id_Usuario' => $usuario->Id_Usuario]);
            event(new Registered($usuario));
        }

        Auth::login($usuario);
        request()->session()->regenerate();

        if ($isNew) {
            try {
                Mail::to($usuario->Usu_Correo)->send(new BienvenidaUsuario($usuario));
                app(WhatsAppService::class)->sendBienvenida($usuario);
                app(AdminNotificationService::class)->usuarioRegistrado($usuario, 'google');
            } catch (Throwable $e) {
                Log::warning('Google register notifications failed', ['message' => $e->getMessage()]);
            }
        }

        $destino = $usuario->esAdministrador()
            ? route('admin.dashboard', absolute: false)
            : route('dashboard', absolute: false);

        return redirect()->intended($destino);
    }
}
