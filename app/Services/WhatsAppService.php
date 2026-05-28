<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsAppService
{
    protected ?Client $twilio = null;

    protected ?string $from = null;

    public function enabled(): bool
    {
        if (! (bool) config('services.twilio.enabled', false)) {
            return false;
        }

        return filled(config('services.twilio.sid'))
            && filled(config('services.twilio.token'))
            && filled(config('services.twilio.whatsapp_from'));
    }

    public function sendMessage(string $to, string $message): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        $toWhatsApp = $this->formatRecipientNumber($to);
        $fromWhatsApp = $this->formatFromNumber((string) config('services.twilio.whatsapp_from'));

        if ($toWhatsApp === null || $fromWhatsApp === null) {
            Log::warning('WhatsApp: número inválido', [
                'to' => $to,
                'to_formatted' => $toWhatsApp,
                'from_config' => config('services.twilio.whatsapp_from'),
                'from_formatted' => $fromWhatsApp,
            ]);

            return false;
        }

        try {
            $this->client()->messages->create($toWhatsApp, [
                'from' => $fromWhatsApp,
                'body' => $message,
            ]);

            return true;
        } catch (\Throwable $exception) {
            Log::error('WhatsApp: error al enviar mensaje', [
                'from' => $fromWhatsApp,
                'to' => $toWhatsApp,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function sendToUsuario(Usuario $usuario, string $message): bool
    {
        $telefono = (string) ($usuario->Usu_Telefono ?? '');

        if ($telefono === '') {
            return false;
        }

        return $this->sendMessage($telefono, $message);
    }

    public function sendBienvenida(Usuario $usuario): bool
    {
        $nombre = (string) ($usuario->Usu_Nombre ?? 'cliente');

        $message = "¡Hola {$nombre}! 🎉\n\n"
            ."Bienvenido a GNA Core. Tu cuenta fue creada correctamente.\n"
            ."Ya puedes explorar la tienda, agregar productos al carrito y realizar tus compras.\n\n"
            .'Gracias por confiar en nosotros.';

        return $this->sendToUsuario($usuario, $message);
    }

    public function sendPasswordReset(Usuario $usuario, string $token): bool
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $usuario->getEmailForPasswordReset(),
        ], false));

        $expire = (int) config('auth.passwords.users.expire', 60);
        $nombre = (string) ($usuario->Usu_Nombre ?? 'cliente');

        $message = "Hola {$nombre},\n\n"
            ."Recibimos una solicitud para restablecer tu contraseña en GNA Core.\n\n"
            ."Usa este enlace (válido {$expire} min):\n{$url}\n\n"
            .'Si no solicitaste este cambio, ignora este mensaje.';

        return $this->sendToUsuario($usuario, $message);
    }

    public function sendPasswordChanged(Usuario $usuario): bool
    {
        $nombre = (string) ($usuario->Usu_Nombre ?? 'cliente');

        $message = "Hola {$nombre},\n\n"
            ."Tu contraseña en GNA Core fue actualizada correctamente.\n\n"
            ."Si NO fuiste tú, cambia tu contraseña de inmediato y contacta soporte.\n\n"
            .'Iniciar sesión: '.url('/login');

        return $this->sendToUsuario($usuario, $message);
    }

    public function sendPedido(Pedido $pedido, bool $pagoConfirmado = false): bool
    {
        $pedido->loadMissing(['usuario', 'pago.metodoPago', 'detalle.producto']);

        $usuario = $pedido->usuario;

        if (! $usuario) {
            return false;
        }

        $lineas = $pedido->detalle ?? collect();
        $subtotal = $lineas->sum(fn ($linea) => (float) $linea->DetaPed_SubTotal);
        $total = (float) $pedido->Ped_TotalPrecio;
        $envio = max(0, $total - $subtotal);
        $metodoPago = $pedido->pago?->metodoPago?->MetPag_Descripcion ?? 'No disponible';
        $nombre = (string) ($usuario->Usu_Nombre ?? 'cliente');

        $encabezado = $pagoConfirmado
            ? "Hola {$nombre}, tu pago del pedido *{$pedido->Ped_Numero}* fue confirmado. ✅"
            : "Hola {$nombre}, registramos tu pedido *{$pedido->Ped_Numero}*.";

        $message = $encabezado."\n\n"
            ."Método de pago: {$metodoPago}\n";

        if (filled($pedido->pago?->Transaccion_Id)) {
            $message .= 'ID transacción: '.$pedido->pago->Transaccion_Id."\n";
        }

        $message .= "\n*Productos:*\n";

        foreach ($lineas as $linea) {
            $nombreProducto = $linea->producto?->Prod_Nombre ?? 'Producto';
            $cantidad = (int) $linea->DetaPed_Cantidad;
            $precio = number_format((float) $linea->DetaPed_Precio, 2);
            $subLinea = number_format((float) $linea->DetaPed_SubTotal, 2);

            $message .= "- {$nombreProducto} x{$cantidad} | Q{$precio} c/u | Sub: Q{$subLinea}\n";
        }

        $message .= "\nSubtotal: Q".number_format($subtotal, 2)."\n"
            .'Envío: Q'.number_format($envio, 2)."\n"
            .'*Total: Q'.number_format($total, 2).'*';

        return $this->sendToUsuario($usuario, $message);
    }

    protected function client(): Client
    {
        if ($this->twilio === null) {
            $this->twilio = new Client(
                (string) config('services.twilio.sid'),
                (string) config('services.twilio.token')
            );
        }

        return $this->twilio;
    }

    /**
     * Número remitente Twilio (sandbox o producción). No aplicar código de país GT.
     * Ejemplo en .env: +14155238886
     */
    protected function formatFromNumber(string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', preg_replace('/^whatsapp:/i', '', trim($number)) ?? '') ?? '';

        if (strlen($digits) < 10) {
            return null;
        }

        return 'whatsapp:+'.$digits;
    }

    /**
     * Destinatario (usuarios en Guatemala por defecto).
     * Ejemplos válidos: 42132791, 50242132791, +50242132791
     */
    protected function formatRecipientNumber(string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', preg_replace('/^whatsapp:/i', '', trim($number)) ?? '') ?? '';

        if ($digits === '') {
            return null;
        }

        $countryCode = preg_replace('/\D+/', '', (string) config('services.twilio.country_code', '502')) ?? '502';

        if (str_starts_with($digits, $countryCode) && strlen($digits) >= strlen($countryCode) + 8) {
            return 'whatsapp:+'.$digits;
        }

        // Otro país explícito (ej. +1...)
        if (strlen($digits) >= 11) {
            return 'whatsapp:+'.$digits;
        }

        if (strlen($digits) === 8) {
            return 'whatsapp:+'.$countryCode.$digits;
        }

        return null;
    }
}
