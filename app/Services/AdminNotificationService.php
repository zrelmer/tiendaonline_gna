<?php

namespace App\Services;

use App\Mail\Admin\AdminAlertMail;
use App\Mail\Admin\AdminStockSemanalMail;
use App\Models\BoletaPago;
use App\Models\Cotizacion;
use App\Models\Pedido;
use App\Models\Usuario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AdminNotificationService
{
    /**
     * @return array<int, string>
     */
    public function destinatarios(): array
    {
        return config('admin_notifications.emails', []);
    }

    public function habilitado(): bool
    {
        if (! config('admin_notifications.enabled', true)) {
            return false;
        }

        return $this->destinatarios() !== [];
    }

    public function pedidoNuevo(Pedido $pedido): void
    {
        $pedido->loadMissing(['usuario', 'pago.metodoPago', 'detalle.producto']);
        $metodo = $pedido->pago?->metodoPago?->MetPag_Descripcion ?? 'No disponible';
        $idMetodo = (int) ($pedido->pago?->Id_MetodoPago ?? 0);

        $titulo = match ($idMetodo) {
            PedidoService::METODO_TARJETA => 'Nuevo pedido con tarjeta (pago pendiente)',
            PedidoService::METODO_CONTRA_ENTREGA => 'Nuevo pedido contra entrega',
            default => 'Nuevo pedido registrado',
        };

        $this->enviar(new AdminAlertMail(
            titulo: $titulo,
            mensaje: 'Se registró un pedido en la tienda que requiere seguimiento administrativo.',
            detalles: $this->detallesPedido($pedido, $metodo),
            accionUrl: route('admin.pedidos.show', $pedido),
            accionTexto: 'Ver pedido en admin',
        ));
    }

    public function pagoTarjetaConfirmado(Pedido $pedido): void
    {
        $pedido->loadMissing(['usuario', 'pago.metodoPago', 'detalle.producto']);
        $metodo = $pedido->pago?->metodoPago?->MetPag_Descripcion ?? 'Tarjeta';

        $this->enviar(new AdminAlertMail(
            titulo: 'Pago con tarjeta confirmado',
            mensaje: 'Recurrente confirmó el pago del pedido. El pedido quedó confirmado.',
            detalles: array_merge(
                $this->detallesPedido($pedido, $metodo),
                array_filter([
                    'ID transacción' => $pedido->pago?->Transaccion_Id,
                ])
            ),
            accionUrl: route('admin.pedidos.show', $pedido),
            accionTexto: 'Ver pedido en admin',
        ));
    }

    public function pagoTarjetaFallido(Pedido $pedido, string $estadoGateway): void
    {
        $pedido->loadMissing(['usuario', 'pago.metodoPago', 'detalle.producto']);
        $metodo = $pedido->pago?->metodoPago?->MetPag_Descripcion ?? 'Tarjeta';

        $this->enviar(new AdminAlertMail(
            titulo: 'Pago con tarjeta fallido o rechazado',
            mensaje: 'Recurrente reportó un pago no exitoso. El pedido fue cancelado automáticamente.',
            detalles: array_merge(
                $this->detallesPedido($pedido, $metodo),
                [
                    'Estado Recurrente' => $estadoGateway,
                ]
            ),
            accionUrl: route('admin.pedidos.show', $pedido),
            accionTexto: 'Ver pedido en admin',
        ));
    }

    public function boletaSubida(Pedido $pedido, BoletaPago $boleta): void
    {
        $pedido->loadMissing(['usuario', 'pago.metodoPago']);

        $this->enviar(new AdminAlertMail(
            titulo: 'Comprobante de transferencia recibido',
            mensaje: 'Un cliente cargó la boleta de pago. Revisa y verifica el comprobante.',
            detalles: array_merge(
                $this->detallesPedido($pedido, 'Transferencia bancaria'),
                [
                    'Boleta ID' => (string) $boleta->Id_Boletapago,
                ]
            ),
            accionUrl: route('admin.boletas.show', $boleta),
            accionTexto: 'Revisar boleta en admin',
        ));
    }

    public function cotizacionSolicitada(Cotizacion $cotizacion): void
    {
        $cotizacion->loadMissing(['usuario', 'detalle']);

        $this->enviar(new AdminAlertMail(
            titulo: 'Nueva solicitud de cotización',
            mensaje: 'Un cliente envió una solicitud de cotización para revisión.',
            detalles: [
                'Número' => $cotizacion->Cot_Numero,
                'Cliente' => $cotizacion->Cot_NombreCliente,
                'Correo' => $cotizacion->Cot_Email ?: ($cotizacion->usuario?->Usu_Correo ?? '—'),
                'Total estimado' => 'Q '.number_format((float) $cotizacion->Cot_Total, 2),
                'Ítems' => (string) $cotizacion->detalle->count(),
            ],
            accionUrl: route('admin.cotizaciones.show', $cotizacion),
            accionTexto: 'Ver cotización en admin',
        ));
    }

    public function cotizacionRespondida(Cotizacion $cotizacion, bool $aceptada, ?string $comentario = null): void
    {
        $cotizacion->loadMissing(['usuario']);

        $this->enviar(new AdminAlertMail(
            titulo: $aceptada ? 'Cotización aceptada por el cliente' : 'Cotización rechazada por el cliente',
            mensaje: $aceptada
                ? 'El cliente aceptó la cotización emitida.'
                : 'El cliente rechazó la cotización emitida.',
            detalles: array_filter([
                'Número' => $cotizacion->Cot_Numero,
                'Cliente' => $cotizacion->Cot_NombreCliente,
                'Correo' => $cotizacion->Cot_Email ?: ($cotizacion->usuario?->Usu_Correo ?? '—'),
                'Total' => 'Q '.number_format((float) $cotizacion->Cot_Total, 2),
                'Comentario' => $comentario,
            ]),
            accionUrl: route('admin.cotizaciones.show', $cotizacion),
            accionTexto: 'Ver cotización en admin',
        ));
    }

    public function usuarioRegistrado(Usuario $usuario, string $origen = 'registro'): void
    {
        $this->enviar(new AdminAlertMail(
            titulo: 'Nuevo usuario registrado',
            mensaje: 'Se creó una cuenta nueva en la tienda.',
            detalles: [
                'Nombre' => $usuario->Usu_Nombre,
                'Correo' => $usuario->Usu_Correo,
                'Teléfono' => $usuario->Usu_Telefono ?: '—',
                'Origen' => $origen === 'google' ? 'Google OAuth' : 'Registro manual',
            ],
            accionUrl: route('admin.usuarios.edit', $usuario),
            accionTexto: 'Ver usuario en admin',
        ));
    }

    public function pedidoCancelado(Pedido $pedido, string $origen): void
    {
        $pedido->loadMissing(['usuario', 'pago.metodoPago']);
        $metodo = $pedido->pago?->metodoPago?->MetPag_Descripcion ?? '—';

        $this->enviar(new AdminAlertMail(
            titulo: 'Pedido cancelado',
            mensaje: 'Un pedido fue cancelado y ya no requiere preparación.',
            detalles: array_merge(
                $this->detallesPedido($pedido, $metodo),
                [
                    'Origen de cancelación' => $origen,
                ]
            ),
            accionUrl: route('admin.pedidos.show', $pedido),
            accionTexto: 'Ver pedido en admin',
        ));
    }

    /**
     * @param  Collection<int, \App\Models\Producto>  $productosBajoStock
     * @param  Collection<int, \App\Models\Producto>  $productosSinStock
     */
    public function resumenStockSemanal(Collection $productosBajoStock, Collection $productosSinStock, int $umbral): void
    {
        if (! $this->habilitado()) {
            return;
        }

        $mail = new AdminStockSemanalMail(
            productosBajoStock: $productosBajoStock,
            productosSinStock: $productosSinStock,
            umbral: $umbral,
            fecha: now()->format('d/m/Y H:i'),
            accionUrl: route('admin.inventario.index', ['filtro' => AdminInventarioService::FILTRO_BAJO_STOCK]),
        );

        $this->enviar($mail);
    }

    /**
     * @return array<string, string>
     */
    protected function detallesPedido(Pedido $pedido, string $metodoPago): array
    {
        return [
            'Número de pedido' => $pedido->Ped_Numero,
            'Cliente' => $pedido->usuario?->Usu_Nombre ?? '—',
            'Correo cliente' => $pedido->usuario?->Usu_Correo ?? '—',
            'Método de pago' => $metodoPago,
            'Total' => 'Q '.number_format((float) $pedido->Ped_TotalPrecio, 2),
        ];
    }

    protected function enviar(object $mail): void
    {
        if (! $this->habilitado()) {
            return;
        }

        try {
            Mail::to($this->destinatarios())->send($mail);
        } catch (Throwable $e) {
            Log::warning('Admin notification mail failed', [
                'mail' => $mail::class,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
