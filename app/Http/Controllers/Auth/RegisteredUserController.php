<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\BienvenidaUsuario;
use App\Http\Controllers\Controller;
use App\Models\Carrito;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Mostrar vista de registro
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Procesar registro de usuario
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // VALIDACIÓN
        $request->validate([
            'Usu_Nombre' => ['required', 'string', 'max:200'],
            'Usu_Correo' => ['required', 'string', 'email', 'max:150', 'unique:tb_usuario,Usu_Correo'],
            'Usu_Telefono' => ['required', 'string', 'max:20'],
            'Usu_Pass' => ['required', 'confirmed', 'min:6'],
        ]);

        // CREACIÓN DE USUARIO
        $user = Usuario::create([
            'Usu_Nombre'   => $request->Usu_Nombre,
            'Usu_Correo'   => $request->Usu_Correo,
            'Usu_Telefono' => $request->Usu_Telefono,
            'Usu_Pass'     => Hash::make($request->Usu_Pass),
            'Id_Rol'       => 2 // 👈 usuario normal
        ]);

        // Un carrito vacío por usuario (Id_Usuario es unique en tb_carrito)
        Carrito::firstOrCreate(['Id_Usuario' => $user->Id_Usuario]);

        // EVENTO DE REGISTRO
        event(new Registered($user));

        // LOGIN AUTOMÁTICO
        Auth::login($user);
        Mail::to($user->Usu_Correo)->send(new BienvenidaUsuario($user));
        // REDIRECCIÓN
        return redirect()->route('dashboard');
    }
}
