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

        return DB::transaction(function () use ($datos, $idUsuario, $lineas) {
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
}
