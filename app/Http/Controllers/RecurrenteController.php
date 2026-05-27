<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class RecurrenteController extends Controller
{
    private $secretKey;
    private $baseUrl = 'https://app.recurrente.com/api/v1';

    public function __construct()
    {
        // Solo cargamos la llave secreta
        $this->secretKey = config('services.recurrente.secret_key');
    }

    public function procesarSuscripcion(Request $request)
    {
        $request->validate([
            'card_token' => 'required|string',
        ]);

        $user = Auth::user();
        $cardToken = $request->input('card_token');

        try {
            // PASO 1: Crear el cliente en Recurrente usando únicamente la llave secreta
            if (!$user->recurrente_customer_id) {
                $responseCliente = Http::withHeaders([
                    'X-SECRET-KEY' => $this->secretKey, // <--- Solo esta cabecera
                ])->post("{$this->baseUrl}/customers", [
                    'email' => $user->email,
                    'name'  => $user->name,
                ]);

                if ($responseCliente->failed()) {
                    return response()->json(['error' => 'No se pudo registrar el cliente en la pasarela.'], 422);
                }

                $user->recurrente_customer_id = $responseCliente->json()['id'];
                $user->save();
            }

            // PASO 2: Crear la suscripción vinculando al Cliente, Token y Plan
            $responseSuscripcion = Http::withHeaders([
                'X-SECRET-KEY' => $this->secretKey,
            ])->post("{$this->baseUrl}/subscriptions", [
                'customer_id' => $user->recurrente_customer_id,
                'card_token'  => $cardToken,
                'plan_id'     => 'prod_pvwtrrmk', // <--- ¡Listo! Ya tiene tu ID real de la URL
            ]);

            if ($responseSuscripcion->successful()) {
                $datosSuscripcion = $responseSuscripcion->json();

                $user->update([
                    'recurrente_subscription_id' => $datosSuscripcion['id'],
                    'subscription_status'        => 'active'
                ]);

                return response()->json(['success' => true]);
            }

            return response()->json(['error' => 'La pasarela rechazó la suscripción.'], 422);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }
}
