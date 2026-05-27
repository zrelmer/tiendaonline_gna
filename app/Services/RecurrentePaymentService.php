<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RecurrentePaymentService
{
    public function createCheckoutUrl(Pedido $pedido): string
    {
        $publicKey = (string) config('services.recurrente.public_key');
        $secretKey = (string) config('services.recurrente.secret_key');
        $baseUrl = rtrim((string) config('services.recurrente.base_url', 'https://app.recurrente.com/api'), '/');
        $currency = (string) config('services.recurrente.currency', 'GTQ');

        if ($publicKey === '' || $secretKey === '') {
            throw new RuntimeException('Falta configurar RECURRENTE_PUBLIC_KEY y RECURRENTE_SECRET_KEY en el entorno.');
        }

        $pedido->loadMissing('detalle.producto.categoria');

        $items = $pedido->detalle->map(function ($linea) use ($currency) {
            return [
                'name' => (string) ($linea->producto?->Prod_Nombre ?? 'Producto'),
                'amount_in_cents' => (int) round(((float) $linea->DetaPed_Precio) * 100),
                'currency' => $currency,
                'quantity' => (int) $linea->DetaPed_Cantidad,
            ];
        })->values()->all();

        $subtotalDetalle = $pedido->detalle->sum(
            fn ($linea) => (float) $linea->DetaPed_Precio * (int) $linea->DetaPed_Cantidad
        );
        $costoEnvio = round((float) $pedido->Ped_TotalPrecio - $subtotalDetalle, 2);

        if ($costoEnvio > 0) {
            $items[] = [
                'name' => 'Costo de envío',
                'amount_in_cents' => (int) round($costoEnvio * 100),
                'currency' => $currency,
                'quantity' => 1,
            ];
        }

        if (empty($items)) {
            throw new RuntimeException('No hay productos para generar el checkout de Recurrente.');
        }

        $response = Http::asJson()
            ->acceptJson()
            ->withHeaders([
                'X-PUBLIC-KEY' => $publicKey,
                'X-SECRET-KEY' => $secretKey,
            ])
            ->post("{$baseUrl}/checkouts", [
                'items' => $items,
                'success_url' => route('cart.checkout.recurrente.success', ['pedido' => $pedido->Id_Pedido]),
                'cancel_url' => route('cart.checkout.recurrente.cancel', ['pedido' => $pedido->Id_Pedido]),
                'reference_id' => (string) $pedido->Ped_Numero,
            ]);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        $payload = $response->json();
        $checkoutUrl = (string) data_get($payload, 'checkout_url', '');

        if ($checkoutUrl === '') {
            throw new RuntimeException('Recurrente no devolvió checkout_url.');
        }

        return $checkoutUrl;
    }
}
