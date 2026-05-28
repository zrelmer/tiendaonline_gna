<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function sendNotification(Request $request, WhatsAppService $whatsAppService): JsonResponse
    {
        if (! $whatsAppService->enabled()) {
            return response()->json([
                'status' => 'error',
                'error_message' => 'WhatsApp no está habilitado o faltan credenciales Twilio en .env',
            ], 422);
        }

        $to = (string) $request->query('to', env('TWILIO_WHATSAPP_TEST_TO', ''));

        if ($to === '') {
            return response()->json([
                'status' => 'error',
                'error_message' => 'Indica ?to=+502XXXXXXXX o configura TWILIO_WHATSAPP_TEST_TO en .env',
            ], 422);
        }

        try {
            $ok = $whatsAppService->sendMessage(
                $to,
                '¡Hola desde GNA Core! Notificación de prueba por WhatsApp. 🚀'
            );

            if (! $ok) {
                return response()->json([
                    'status' => 'error',
                    'error_message' => 'No se pudo enviar el mensaje. Revisa el número y los logs.',
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'info' => 'Mensaje enviado correctamente mediante WhatsApp.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ], 500);
        }
    }
}
