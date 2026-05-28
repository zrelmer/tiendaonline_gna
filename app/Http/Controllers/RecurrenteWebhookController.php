<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoHistorial;
use App\Services\PedidoService;
use App\Support\EstatusCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecurrenteWebhookController extends Controller
{
    public function __construct(
        protected PedidoService $pedidoService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->all();

        $referenceId = (string) data_get($payload, 'reference_id', data_get($payload, 'data.reference_id', ''));
        $transactionId = (string) data_get($payload, 'id', data_get($payload, 'data.id', ''));
        $status = strtolower((string) data_get($payload, 'status', data_get($payload, 'data.status', '')));

        if ($referenceId === '' || $status === '') {
            return response()->json(['ok' => false, 'message' => 'Payload incompleto'], 422);
        }

        $pedido = Pedido::query()
            ->where('Ped_Numero', $referenceId)
            ->first();

        if (! $pedido) {
            return response()->json(['ok' => false, 'message' => 'Pedido no encontrado'], 404);
        }

        [$idEstatusPago, $idEstatusPedido, $comentario] = $this->mapStatus($status);

        DB::transaction(function () use ($pedido, $payload, $transactionId, $idEstatusPago, $idEstatusPedido, $comentario, $status): void {
            $pago = Pago::query()->where('Id_Pedido', $pedido->Id_Pedido)->first();

            if (! $pago) {
                return;
            }

            $pago->update([
                'Transaccion_Id' => $transactionId !== '' ? $transactionId : $pago->Transaccion_Id,
                'Transaccion_Json' => [
                    ...(is_array($pago->Transaccion_Json) ? $pago->Transaccion_Json : []),
                    'gateway' => 'recurrente',
                    'estado' => $status,
                    'webhook' => $payload,
                ],
                'Id_Estatus' => $idEstatusPago,
            ]);

            $pedido->update([
                'Id_Estatus' => $idEstatusPedido,
            ]);

            PedidoHistorial::create([
                'Id_Pedido' => $pedido->Id_Pedido,
                'Id_Estatus' => $idEstatusPedido,
                'Comentario' => $comentario,
                'Fecha_Cambio' => now(),
            ]);
        });

        if ($idEstatusPago === EstatusCatalog::PAGO_PAGADO) {
            $this->pedidoService->enviarNotificacionesPedido($pedido->fresh(), true);
        }

        return response()->json(['ok' => true]);
    }

    private function mapStatus(string $status): array
    {
        $paidStatuses = ['paid', 'approved', 'succeeded', 'successful', 'completed'];
        $failedStatuses = ['failed', 'rejected', 'declined', 'canceled', 'cancelled', 'voided', 'expired'];

        if (in_array($status, $paidStatuses, true)) {
            return [
                EstatusCatalog::PAGO_PAGADO,
                EstatusCatalog::PEDIDO_CONFIRMADO,
                'Pago con tarjeta confirmado por webhook de Recurrente.',
            ];
        }

        if (in_array($status, $failedStatuses, true)) {
            return [
                EstatusCatalog::PAGO_RECHAZADO,
                EstatusCatalog::PEDIDO_CANCELADO,
                'Pago con tarjeta rechazado/cancelado según webhook de Recurrente.',
            ];
        }

        return [
            EstatusCatalog::PAGO_PENDIENTE_VERIFICACION,
            EstatusCatalog::PEDIDO_PENDIENTE,
            'Webhook de Recurrente recibido con estado pendiente/intermedio.',
        ];
    }
}
