<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardPasswordUpdateRequest;
use App\Http\Requests\DashboardProfileUpdateRequest;
use App\Notifications\PasswordChangedNotification;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class DashboardProfileController extends Controller
{
    public function __construct(
        protected WhatsAppService $whatsAppService
    ) {}

    public function update(DashboardProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()
            ->route('dashboard')
            ->with('success', 'Tu información de perfil fue actualizada.')
            ->with('tab', 'profile');
    }

    public function updatePassword(DashboardPasswordUpdateRequest $request): RedirectResponse
    {
        $usuario = $request->user();

        $usuario->update([
            'Usu_Pass' => Hash::make($request->validated('password')),
        ]);

        $usuario->notify(new PasswordChangedNotification());
        $this->whatsAppService->sendPasswordChanged($usuario);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Tu contraseña fue actualizada correctamente.')
            ->with('tab', 'profile');
    }
}
