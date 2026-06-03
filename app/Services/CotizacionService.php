<?php

namespace App\Services;

use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\CotizacionHistorial;
use App\Models\Producto;
use App\Models\Usuario;
use App\Support\EstatusCatalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CotizacionService
{
    public function __construct(
        protected AdminNotificationService $adminNotificationService
    ) {}

    /**
     * @param  array{
     *     nombre_cliente: string,
     *     nit?: string|null,
     *     direccion?: string|null,
     *     email?: string|null,
     *     notas?: string|null,
     *     items: array<int, array{id_producto?: int|null, descripcion?: string|null, cantidad: int}>
     * }  $datos
     */
    public function crearSolicitud(array $datos, ?int $idUsuario = null): Cotizacion
    {
        $idUsuario ??= (int) Auth::user()->Id_Usuario;
        $lineas = $this->normalizarLineas($datos['items'] ?? []);

        if ($lineas === []) {
            throw ValidationException::withMessages([
                'items' => 'Debes agregar al menos un producto o descripción a la solicitud.',
            ]);
        }

        $cotizacion = DB::transaction(function () use ($datos, $idUsuario, $lineas) {
            $subtotal = round(collect($lineas)->sum('subtotal'), 2);

            $cotizacion = Cotizacion::create([
                'Id_Usuario' => $idUsuario,
                'Cot_Numero' => $this->generarNumeroSolicitud(),
                'Cot_NombreCliente' => $datos['nombre_cliente'],
                'Cot_Nit' => $datos['nit'] ?? null,
                'Cot_Direccion' => $datos['direccion'] ?? null,
                'Cot_Email' => $datos['email'] ?? null,
                'Cot_NotasSolicitud' => $datos['notas'] ?? null,
                'Cot_Subtotal' => $subtotal,
                'Cot_Total' => $subtotal,
                'Cot_VigenciaDias' => (int) config('cotizacion.vigencia_dias', 10),
                'Cot_Terminos' => null,
                'Id_Estatus' => EstatusCatalog::COTIZACION_SOLICITUD_RECIBIDA,
            ]);

            foreach ($lineas as $linea) {
                CotizacionDetalle::create([
                    'Id_Cotizacion' => $cotizacion->Id_Cotizacion,
                    'Id_Producto' => $linea['id_producto'],
                    'Cantidad' => $linea['cantidad'],
                    'Descripcion' => $linea['descripcion'],
                    'Costo_Unit' => $linea['costo_unit'],
                    'Subtotal' => $linea['subtotal'],
                ]);
            }

            CotizacionHistorial::create([
                'Id_Cotizacion' => $cotizacion->Id_Cotizacion,
                'Id_Estatus' => EstatusCatalog::COTIZACION_SOLICITUD_RECIBIDA,
                'Comentario' => 'Solicitud de cotización registrada. Nuestro equipo la revisará pronto.',
                'Fecha_Cambio' => now(),
            ]);

            return $cotizacion->load(['estatus', 'detalle.producto', 'historial.estatus']);
        });

        $this->adminNotificationService->cotizacionSolicitada($cotizacion);

        return $cotizacion;
    }

    public function cotizacionDelUsuario(int $idCotizacion, ?int $idUsuario = null): Cotizacion
    {
        $idUsuario ??= (int) Auth::user()->Id_Usuario;

        return Cotizacion::query()
            ->where('Id_Cotizacion', $idCotizacion)
            ->where('Id_Usuario', $idUsuario)
            ->with(['estatus', 'detalle.producto', 'historial.estatus'])
            ->firstOrFail();
    }

    /**
     * @param  array<int, array{id_producto?: mixed, descripcion?: mixed, cantidad?: mixed}>  $items
     * @return array<int, array{id_producto: ?int, descripcion: string, cantidad: int, costo_unit: float, subtotal: float}>
     */
    protected function normalizarLineas(array $items): array
    {
        $lineas = [];

        foreach ($items as $item) {
            $cantidad = (int) ($item['cantidad'] ?? 0);
            $idProducto = isset($item['id_producto']) && $item['id_producto'] !== ''
                ? (int) $item['id_producto']
                : null;
            $descripcion = trim((string) ($item['descripcion'] ?? ''));

            if ($cantidad < 1) {
                continue;
            }

            $costoUnit = 0.0;

            if ($idProducto) {
                $producto = Producto::query()
                    ->where('Id_Producto', $idProducto)
                    ->where('Prod_Activo', 1)
                    ->first();

                if (! $producto) {
                    throw ValidationException::withMessages([
                        'items' => 'Uno de los productos seleccionados no está disponible.',
                    ]);
                }

                $descripcion = $producto->Prod_Nombre;
                $costoUnit = (float) $producto->Prod_Precio;
            }

            if ($descripcion === '') {
                continue;
            }

            $subtotal = round($costoUnit * $cantidad, 2);

            $lineas[] = [
                'id_producto' => $idProducto,
                'descripcion' => Str::limit($descripcion, 500, ''),
                'cantidad' => $cantidad,
                'costo_unit' => $costoUnit,
                'subtotal' => $subtotal,
            ];
        }

        return $lineas;
    }

    protected function generarNumeroSolicitud(): string
    {
        do {
            $numero = 'SOL-'.strtoupper(Str::random(8));
        } while (Cotizacion::query()->where('Cot_Numero', $numero)->exists());

        return $numero;
    }

    /**
     * Marca como vencidas las cotizaciones emitidas cuyo plazo ya expiró.
     */
    public function sincronizarVencidas(?int $idUsuario = null): int
    {
        $query = Cotizacion::query()
            ->where('Id_Estatus', EstatusCatalog::COTIZACION_EMITIDA)
            ->whereNotNull('Cot_FechaEmision');

        if ($idUsuario !== null) {
            $query->where('Id_Usuario', $idUsuario);
        }

        $actualizadas = 0;

        foreach ($query->get() as $cotizacion) {
            if (! $cotizacion->estaVencidaPorFecha()) {
                continue;
            }

            $this->marcarVencida($cotizacion);
            $actualizadas++;
        }

        return $actualizadas;
    }

    public function aceptar(Cotizacion $cotizacion, ?string $comentario = null, ?int $idUsuario = null): Cotizacion
    {
        $cotizacion = $this->cotizacionDelUsuario((int) $cotizacion->Id_Cotizacion, $idUsuario);

        $this->sincronizarVencidas((int) $cotizacion->Id_Usuario);
        $cotizacion->refresh();

        if ($motivo = $this->motivoNoAceptar($cotizacion)) {
            throw ValidationException::withMessages([
                'cotizacion' => $motivo,
            ]);
        }

        $cotizacion = DB::transaction(function () use ($cotizacion, $comentario) {
            $cotizacion->update([
                'Id_Estatus' => EstatusCatalog::COTIZACION_ACEPTADA,
            ]);

            CotizacionHistorial::create([
                'Id_Cotizacion' => $cotizacion->Id_Cotizacion,
                'Id_Estatus' => EstatusCatalog::COTIZACION_ACEPTADA,
                'Comentario' => $comentario ?: 'Cotización aceptada por el cliente.',
                'Fecha_Cambio' => now(),
            ]);

            return $cotizacion->fresh(['estatus', 'detalle.producto', 'historial.estatus']);
        });

        $this->adminNotificationService->cotizacionRespondida($cotizacion, true, $comentario);

        return $cotizacion;
    }

    public function rechazar(Cotizacion $cotizacion, ?string $comentario = null, ?int $idUsuario = null): Cotizacion
    {
        $cotizacion = $this->cotizacionDelUsuario((int) $cotizacion->Id_Cotizacion, $idUsuario);

        $this->sincronizarVencidas((int) $cotizacion->Id_Usuario);
        $cotizacion->refresh();

        if ($motivo = $this->motivoNoRechazar($cotizacion)) {
            throw ValidationException::withMessages([
                'cotizacion' => $motivo,
            ]);
        }

        $cotizacion = DB::transaction(function () use ($cotizacion, $comentario) {
            $cotizacion->update([
                'Id_Estatus' => EstatusCatalog::COTIZACION_RECHAZADA,
            ]);

            CotizacionHistorial::create([
                'Id_Cotizacion' => $cotizacion->Id_Cotizacion,
                'Id_Estatus' => EstatusCatalog::COTIZACION_RECHAZADA,
                'Comentario' => $comentario ?: 'Cotización rechazada por el cliente.',
                'Fecha_Cambio' => now(),
            ]);

            return $cotizacion->fresh(['estatus', 'detalle.producto', 'historial.estatus']);
        });

        $this->adminNotificationService->cotizacionRespondida($cotizacion, false, $comentario);

        return $cotizacion;
    }

    public function motivoNoAceptar(Cotizacion $cotizacion): ?string
    {
        if ((int) $cotizacion->Id_Estatus === EstatusCatalog::COTIZACION_VENCIDA) {
            return 'Esta cotización ya venció. Solicita una nueva si aún la necesitas.';
        }

        if ((int) $cotizacion->Id_Estatus === EstatusCatalog::COTIZACION_ACEPTADA) {
            return 'Esta cotización ya fue aceptada.';
        }

        if ((int) $cotizacion->Id_Estatus === EstatusCatalog::COTIZACION_RECHAZADA) {
            return 'Esta cotización ya fue rechazada.';
        }

        if ((int) $cotizacion->Id_Estatus !== EstatusCatalog::COTIZACION_EMITIDA) {
            return 'Solo puedes aceptar cotizaciones que ya fueron emitidas por el equipo.';
        }

        if ($cotizacion->estaVencidaPorFecha()) {
            return 'El plazo de vigencia de esta cotización expiró.';
        }

        return null;
    }

    public function motivoNoRechazar(Cotizacion $cotizacion): ?string
    {
        if ((int) $cotizacion->Id_Estatus === EstatusCatalog::COTIZACION_VENCIDA) {
            return 'Esta cotización ya venció.';
        }

        if ((int) $cotizacion->Id_Estatus === EstatusCatalog::COTIZACION_ACEPTADA) {
            return 'No puedes rechazar una cotización que ya aceptaste.';
        }

        if ((int) $cotizacion->Id_Estatus === EstatusCatalog::COTIZACION_RECHAZADA) {
            return 'Esta cotización ya fue rechazada.';
        }

        if ((int) $cotizacion->Id_Estatus !== EstatusCatalog::COTIZACION_EMITIDA) {
            return 'Solo puedes rechazar cotizaciones emitidas pendientes de tu respuesta.';
        }

        if ($cotizacion->estaVencidaPorFecha()) {
            return 'El plazo de vigencia de esta cotización expiró.';
        }

        return null;
    }

    protected function marcarVencida(Cotizacion $cotizacion): Cotizacion
    {
        if ((int) $cotizacion->Id_Estatus !== EstatusCatalog::COTIZACION_EMITIDA) {
            return $cotizacion;
        }

        return DB::transaction(function () use ($cotizacion) {
            $vencimiento = $cotizacion->fechaVencimiento();

            $cotizacion->update([
                'Id_Estatus' => EstatusCatalog::COTIZACION_VENCIDA,
            ]);

            CotizacionHistorial::create([
                'Id_Cotizacion' => $cotizacion->Id_Cotizacion,
                'Id_Estatus' => EstatusCatalog::COTIZACION_VENCIDA,
                'Comentario' => $vencimiento
                    ? 'Vigencia vencida el '.$vencimiento->format('d/m/Y H:i').'.'
                    : 'Vigencia de la cotización vencida.',
                'Fecha_Cambio' => now(),
            ]);

            return $cotizacion->fresh(['estatus', 'historial.estatus']);
        });
    }
}
