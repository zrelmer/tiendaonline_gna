<?php

namespace App\Services;

use App\Models\DetallePedido;
use App\Models\Direccion;
use App\Models\Envio;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoHistorial;
use App\Support\EstatusCatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PedidoService
{
    public const METODO_TRANSFERENCIA = 2;

    public const METODO_CONTRA_ENTREGA = 3;

    public function __construct(
        protected CarritoService $carritoService
    ) {}

    public function crearDesdeCheckout(int $idDireccion, int $idMetodoPago, ?int $idUsuario = null): Pedido
    {
        $configPago = match ($idMetodoPago) {
            self::METODO_TRANSFERENCIA => [
                'id_estatus_pago' => EstatusCatalog::PAGO_PENDIENTE_COMPROBANTE,
                'transaccion' => [
                    'tipo' => 'transferencia',
                    'estado' => 'pendiente_comprobante',
                ],
                'historial' => 'Pedido registrado. Pendiente de transferencia bancaria y comprobante de pago.',
            ],
            self::METODO_CONTRA_ENTREGA => [
                'id_estatus_pago' => EstatusCatalog::PAGO_PENDIENTE_COBRO,
                'transaccion' => [
                    'tipo' => 'contra_entrega',
                    'cobro' => 'pendiente_al_entregar',
                ],
                'historial' => 'Pedido registrado. Pago contra entrega pendiente al momento de la entrega.',
            ],
            default => throw ValidationException::withMessages([
                'id_metodo_pago' => 'Método de pago no disponible.',
            ]),
        };

        return $this->crearPedido($idDireccion, $idMetodoPago, $configPago, $idUsuario);
    }

    protected function crearPedido(
        int $idDireccion,
        int $idMetodoPago,
        array $configPago,
        ?int $idUsuario = null
    ): Pedido {
        $idUsuario ??= $this->carritoService->idUsuarioAutenticado();

        return DB::transaction(function () use ($idDireccion, $idMetodoPago, $configPago, $idUsuario) {
            $detalles = $this->carritoService->detallesCarrito($idUsuario);

            if ($detalles->isEmpty()) {
                throw ValidationException::withMessages([
                    'carrito' => 'Tu carrito está vacío. Agrega productos antes de confirmar.',
                ]);
            }

            $direccion = Direccion::query()
                ->where('Id_Direccion', $idDireccion)
                ->where('Id_Usuario', $idUsuario)
                ->with(['municipio.departamento'])
                ->firstOrFail();

            $subtotal = $detalles->sum(
                fn ($linea) => (float) $linea->Precio * (int) $linea->Cantidad
            );
            $envio = $this->costoEnvio($subtotal);
            $total = round($subtotal + $envio, 2);

            $pedido = Pedido::create([
                'Id_Usuario' => $idUsuario,
                'Id_Direccion' => $direccion->Id_Direccion,
                'Ped_Numero' => $this->generarNumeroPedido(),
                'Ped_TotalPrecio' => $total,
                'Id_Estatus' => EstatusCatalog::PEDIDO_PENDIENTE,
            ]);

            foreach ($detalles as $linea) {
                $cantidad = (int) $linea->Cantidad;
                $precio = (float) $linea->Precio;

                DetallePedido::create([
                    'Id_Pedido' => $pedido->Id_Pedido,
                    'Id_Producto' => $linea->Id_Producto,
                    'DetaPed_Cantidad' => $cantidad,
                    'DetaPed_Precio' => $precio,
                    'DetaPed_SubTotal' => round($precio * $cantidad, 2),
                ]);
            }

            Pago::create([
                'Id_Pedido' => $pedido->Id_Pedido,
                'Id_MetodoPago' => $idMetodoPago,
                'Transaccion_Id' => null,
                'Transaccion_Json' => $configPago['transaccion'],
                'Id_Estatus' => $configPago['id_estatus_pago'],
            ]);

            Envio::create([
                'Id_Pedido' => $pedido->Id_Pedido,
                'Direccion_Envio' => $this->textoDireccionEnvio($direccion),
                'Empresa_Envio' => null,
                'Numero_Guia' => null,
                'Fecha_Envio' => now(),
                'Fecha_Entrega' => null,
                'Id_Estatus' => EstatusCatalog::ENVIO_PENDIENTE,
            ]);

            PedidoHistorial::create([
                'Id_Pedido' => $pedido->Id_Pedido,
                'Id_Estatus' => EstatusCatalog::PEDIDO_PENDIENTE,
                'Comentario' => $configPago['historial'],
                'Fecha_Cambio' => now(),
            ]);

            $this->carritoService->vaciarCarrito($idUsuario);

            return $pedido->load(['detalle.producto', 'pago', 'envio']);
        });
    }

    protected function costoEnvio(float $subtotal): float
    {
        return $subtotal < 300 ? 35.0 : 0.0;
    }

    protected function generarNumeroPedido(): string
    {
        do {
            $numero = 'PED-'.strtoupper(Str::random(8));
        } while (Pedido::query()->where('Ped_Numero', $numero)->exists());

        return $numero;
    }

    protected function textoDireccionEnvio(Direccion $direccion): string
    {
        $partes = array_filter([
            $direccion->Direccion,
            $direccion->municipio?->Nom_Municipio,
            $direccion->municipio?->departamento?->Nom_Departamento,
        ]);

        return Str::limit(implode(', ', $partes), 200, '');
    }
}
