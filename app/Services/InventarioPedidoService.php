<?php

namespace App\Services;

use App\Models\DetallePedido;
use App\Models\Inventario;
use App\Models\InventarioHistorial;
use App\Models\Movimiento;
use App\Models\Pedido;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class InventarioPedidoService
{
    private const MOVIMIENTO_SALIDA = 'Salida por pedido';

    private const MOVIMIENTO_DEVOLUCION = 'Devolución por cancelación';

    private const MOVIMIENTO_RESERVA = 'Reserva por pedido';

    private const MOVIMIENTO_LIBERACION = 'Liberación de reserva';

    /**
     * @param  Collection<int, \App\Models\CarritoDetalle>  $lineasCarrito
     */
    public function validarStockParaCarrito(Collection $lineasCarrito): void
    {
        $requeridos = [];

        foreach ($lineasCarrito as $linea) {
            $idProducto = (int) $linea->Id_Producto;
            $requeridos[$idProducto] = ($requeridos[$idProducto] ?? 0) + (int) $linea->Cantidad;
        }

        $this->validarCantidadesRequeridas($requeridos, $lineasCarrito->first()?->producto?->Prod_Nombre);
    }

    public function validarStockParaPedido(Pedido $pedido): void
    {
        $pedido->loadMissing('detalle.producto');

        $requeridos = [];

        foreach ($pedido->detalle as $linea) {
            $idProducto = (int) $linea->Id_Producto;
            $requeridos[$idProducto] = ($requeridos[$idProducto] ?? 0) + (int) $linea->DetaPed_Cantidad;
        }

        $this->validarCantidadesRequeridas($requeridos);
    }

    public function reservarPorPedido(Pedido $pedido): void
    {
        if ($this->yaReservado($pedido)) {
            return;
        }

        $pedido->loadMissing('detalle.producto');

        if ($pedido->detalle->isEmpty()) {
            return;
        }

        $idMovimiento = $this->idMovimientoReserva();

        foreach ($pedido->detalle as $linea) {
            $referencia = $this->referenciaReservaProducto($pedido, $linea);

            if (InventarioHistorial::query()->where('Referencia', $referencia)->exists()) {
                continue;
            }

            $cantidad = (int) $linea->DetaPed_Cantidad;

            if ($cantidad <= 0) {
                continue;
            }

            $inventario = Inventario::query()
                ->where('Id_Producto', $linea->Id_Producto)
                ->lockForUpdate()
                ->first();

            $nombreProducto = $linea->producto?->Prod_Nombre ?? 'Producto #'.$linea->Id_Producto;

            if (! $inventario) {
                throw ValidationException::withMessages([
                    'pedido' => "El producto «{$nombreProducto}» no tiene inventario registrado.",
                ]);
            }

            $stock = (int) $inventario->Stock;
            $reservado = (int) $inventario->Stock_Reservado;
            $disponible = $stock - $reservado;

            if ($disponible < $cantidad) {
                throw ValidationException::withMessages([
                    'pedido' => "Stock insuficiente para «{$nombreProducto}». Disponible: {$disponible}.",
                ]);
            }

            $reservadoDespues = $reservado + $cantidad;

            $inventario->update([
                'Stock_Reservado' => $reservadoDespues,
            ]);

            InventarioHistorial::create([
                'Id_Inventario' => $inventario->Id_Inventario,
                'Id_Movimiento' => $idMovimiento,
                'Cantidad' => $cantidad,
                'Stock_Antes' => $stock,
                'Stock_Despues' => $stock,
                'Referencia' => $referencia,
                'Fecha_Movimiento' => now(),
            ]);
        }
    }

    public function liberarReservaPorPedido(Pedido $pedido): void
    {
        if ($this->yaDescontado($pedido) || ! $this->yaReservado($pedido)) {
            return;
        }

        $pedido->loadMissing('detalle.producto');

        $idMovimiento = $this->idMovimientoLiberacion();

        foreach ($pedido->detalle as $linea) {
            $referenciaReserva = $this->referenciaReservaProducto($pedido, $linea);
            $referenciaLiberacion = $this->referenciaLiberacionProducto($pedido, $linea);

            if (! InventarioHistorial::query()->where('Referencia', $referenciaReserva)->exists()) {
                continue;
            }

            if (InventarioHistorial::query()->where('Referencia', $referenciaLiberacion)->exists()) {
                continue;
            }

            $cantidad = (int) $linea->DetaPed_Cantidad;

            if ($cantidad <= 0) {
                continue;
            }

            $inventario = Inventario::query()
                ->where('Id_Producto', $linea->Id_Producto)
                ->lockForUpdate()
                ->first();

            if (! $inventario) {
                continue;
            }

            $stock = (int) $inventario->Stock;
            $reservado = (int) $inventario->Stock_Reservado;
            $reservadoDespues = max(0, $reservado - $cantidad);

            $inventario->update([
                'Stock_Reservado' => $reservadoDespues,
            ]);

            InventarioHistorial::create([
                'Id_Inventario' => $inventario->Id_Inventario,
                'Id_Movimiento' => $idMovimiento,
                'Cantidad' => $cantidad,
                'Stock_Antes' => $stock,
                'Stock_Despues' => $stock,
                'Referencia' => $referenciaLiberacion,
                'Fecha_Movimiento' => now(),
            ]);
        }
    }

    public function descontarPorConfirmacion(Pedido $pedido): void
    {
        if ($this->yaDescontado($pedido)) {
            return;
        }

        $pedido->loadMissing('detalle.producto');

        if ($pedido->detalle->isEmpty()) {
            return;
        }

        $idMovimiento = $this->idMovimientoSalida();
        $pedidoTeniaReserva = $this->yaReservado($pedido);

        foreach ($pedido->detalle as $linea) {
            $referenciaSalida = $this->referenciaSalidaProducto($pedido, $linea);

            if (InventarioHistorial::query()->where('Referencia', $referenciaSalida)->exists()) {
                continue;
            }

            $cantidad = (int) $linea->DetaPed_Cantidad;

            if ($cantidad <= 0) {
                continue;
            }

            $inventario = Inventario::query()
                ->where('Id_Producto', $linea->Id_Producto)
                ->lockForUpdate()
                ->first();

            $nombreProducto = $linea->producto?->Prod_Nombre ?? 'Producto #'.$linea->Id_Producto;

            if (! $inventario) {
                throw ValidationException::withMessages([
                    'pedido' => "El producto «{$nombreProducto}» no tiene inventario registrado.",
                ]);
            }

            $stockAntes = (int) $inventario->Stock;
            $reservado = (int) $inventario->Stock_Reservado;
            $lineaReservada = $pedidoTeniaReserva
                && InventarioHistorial::query()
                    ->where('Referencia', $this->referenciaReservaProducto($pedido, $linea))
                    ->exists();

            if ($lineaReservada) {
                if ($stockAntes < $cantidad) {
                    throw ValidationException::withMessages([
                        'pedido' => "Stock insuficiente para «{$nombreProducto}» al confirmar el pedido.",
                    ]);
                }
            } else {
                $disponible = $stockAntes - $reservado;

                if ($disponible < $cantidad) {
                    throw ValidationException::withMessages([
                        'pedido' => "Stock insuficiente para «{$nombreProducto}». Disponible: {$disponible}, requerido: {$cantidad}.",
                    ]);
                }
            }

            $stockDespues = $stockAntes - $cantidad;
            $reservadoDespues = $lineaReservada
                ? max(0, $reservado - $cantidad)
                : $reservado;

            $inventario->update([
                'Stock' => $stockDespues,
                'Stock_Reservado' => $reservadoDespues,
            ]);

            InventarioHistorial::create([
                'Id_Inventario' => $inventario->Id_Inventario,
                'Id_Movimiento' => $idMovimiento,
                'Cantidad' => $cantidad,
                'Stock_Antes' => $stockAntes,
                'Stock_Despues' => $stockDespues,
                'Referencia' => $referenciaSalida,
                'Fecha_Movimiento' => now(),
            ]);
        }
    }

    public function reponerPorCancelacion(Pedido $pedido): void
    {
        if (! $this->yaDescontado($pedido) || $this->yaRepuestoPorCancelacion($pedido)) {
            return;
        }

        $pedido->loadMissing('detalle.producto');

        if ($pedido->detalle->isEmpty()) {
            return;
        }

        $idMovimiento = $this->idMovimientoDevolucion();

        foreach ($pedido->detalle as $linea) {
            $referenciaSalida = $this->referenciaSalidaProducto($pedido, $linea);
            $referenciaDevolucion = $this->referenciaDevolucionProducto($pedido, $linea);

            if (! InventarioHistorial::query()->where('Referencia', $referenciaSalida)->exists()) {
                continue;
            }

            if (InventarioHistorial::query()->where('Referencia', $referenciaDevolucion)->exists()) {
                continue;
            }

            $cantidad = (int) $linea->DetaPed_Cantidad;

            if ($cantidad <= 0) {
                continue;
            }

            $inventario = Inventario::query()
                ->where('Id_Producto', $linea->Id_Producto)
                ->lockForUpdate()
                ->first();

            if (! $inventario) {
                continue;
            }

            $stockAntes = (int) $inventario->Stock;
            $stockDespues = $stockAntes + $cantidad;

            $inventario->update([
                'Stock' => $stockDespues,
            ]);

            InventarioHistorial::create([
                'Id_Inventario' => $inventario->Id_Inventario,
                'Id_Movimiento' => $idMovimiento,
                'Cantidad' => $cantidad,
                'Stock_Antes' => $stockAntes,
                'Stock_Despues' => $stockDespues,
                'Referencia' => $referenciaDevolucion,
                'Fecha_Movimiento' => now(),
            ]);
        }
    }

    public function yaDescontado(Pedido $pedido): bool
    {
        return InventarioHistorial::query()
            ->where('Referencia', 'like', 'PEDIDO:'.$pedido->Id_Pedido.':SALIDA:%')
            ->exists();
    }

    public function yaReservado(Pedido $pedido): bool
    {
        return InventarioHistorial::query()
            ->where('Referencia', 'like', 'PEDIDO:'.$pedido->Id_Pedido.':RESERVA:%')
            ->exists();
    }

    public function yaRepuestoPorCancelacion(Pedido $pedido): bool
    {
        return InventarioHistorial::query()
            ->where('Referencia', 'like', 'PEDIDO:'.$pedido->Id_Pedido.':DEVOLUCION:%')
            ->exists();
    }

    /**
     * @param  array<int, int>  $requeridos
     */
    private function validarCantidadesRequeridas(array $requeridos, ?string $nombreFallback = null): void
    {
        if ($requeridos === []) {
            return;
        }

        $inventarios = Inventario::query()
            ->with('producto')
            ->whereIn('Id_Producto', array_keys($requeridos))
            ->get()
            ->keyBy('Id_Producto');

        foreach ($requeridos as $idProducto => $cantidadRequerida) {
            $inventario = $inventarios->get($idProducto);
            $nombreProducto = $inventario?->producto?->Prod_Nombre
                ?? $nombreFallback
                ?? 'Producto #'.$idProducto;

            if (! $inventario) {
                throw ValidationException::withMessages([
                    'carrito' => "El producto «{$nombreProducto}» no tiene inventario registrado.",
                ]);
            }

            $disponible = (int) $inventario->Stock - (int) $inventario->Stock_Reservado;

            if ($disponible < $cantidadRequerida) {
                throw ValidationException::withMessages([
                    'carrito' => "Stock insuficiente para «{$nombreProducto}». Disponible: {$disponible}.",
                ]);
            }
        }
    }

    private function idMovimientoSalida(): int
    {
        return (int) Movimiento::query()->firstOrCreate([
            'Nom_Movimiento' => self::MOVIMIENTO_SALIDA,
        ])->Id_Movimiento;
    }

    private function idMovimientoDevolucion(): int
    {
        return (int) Movimiento::query()->firstOrCreate([
            'Nom_Movimiento' => self::MOVIMIENTO_DEVOLUCION,
        ])->Id_Movimiento;
    }

    private function idMovimientoReserva(): int
    {
        return (int) Movimiento::query()->firstOrCreate([
            'Nom_Movimiento' => self::MOVIMIENTO_RESERVA,
        ])->Id_Movimiento;
    }

    private function idMovimientoLiberacion(): int
    {
        return (int) Movimiento::query()->firstOrCreate([
            'Nom_Movimiento' => self::MOVIMIENTO_LIBERACION,
        ])->Id_Movimiento;
    }

    private function referenciaSalidaProducto(Pedido $pedido, DetallePedido $linea): string
    {
        return 'PEDIDO:'.$pedido->Id_Pedido.':SALIDA:'.$linea->Id_Producto;
    }

    private function referenciaDevolucionProducto(Pedido $pedido, DetallePedido $linea): string
    {
        return 'PEDIDO:'.$pedido->Id_Pedido.':DEVOLUCION:'.$linea->Id_Producto;
    }

    private function referenciaReservaProducto(Pedido $pedido, DetallePedido $linea): string
    {
        return 'PEDIDO:'.$pedido->Id_Pedido.':RESERVA:'.$linea->Id_Producto;
    }

    private function referenciaLiberacionProducto(Pedido $pedido, DetallePedido $linea): string
    {
        return 'PEDIDO:'.$pedido->Id_Pedido.':LIBERA:'.$linea->Id_Producto;
    }
}
