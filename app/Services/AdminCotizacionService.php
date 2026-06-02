<?php

namespace App\Services;

use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\CotizacionHistorial;
use App\Support\EstatusCatalog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminCotizacionService
{
    public const ACCION_REVISION = 'revision';

    public const ACCION_EN_REVISION = 'en_revision';

    /**
     * @return self::ACCION_*|null
     */
    public function accionDisponible(Cotizacion $cotizacion): ?string
    {
        return match ((int) $cotizacion->Id_Estatus) {
            EstatusCatalog::COTIZACION_SOLICITUD_RECIBIDA => self::ACCION_REVISION,
            EstatusCatalog::COTIZACION_EN_REVISION => self::ACCION_EN_REVISION,
            default => null,
        };
    }

    public function etiquetaAccion(?string $accion): string
    {
        return match ($accion) {
            self::ACCION_REVISION => 'Marcar en revisión',
            self::ACCION_EN_REVISION => 'Emitir cotización',
            default => '—',
        };
    }

    /**
     * @return self::ACCION_*|null
     */
    public function accionDesdeFiltro(?string $filtro): ?string
    {
        $filtro = trim((string) $filtro);

        if ($filtro === '') {
            return null;
        }

        $permitidos = [
            self::ACCION_REVISION,
            self::ACCION_EN_REVISION,
        ];

        return in_array($filtro, $permitidos, true) ? $filtro : null;
    }

    public function estatusIdParaAccion(string $accion): ?int
    {
        return match ($accion) {
            self::ACCION_REVISION => EstatusCatalog::COTIZACION_SOLICITUD_RECIBIDA,
            self::ACCION_EN_REVISION => EstatusCatalog::COTIZACION_EN_REVISION,
            default => null,
        };
    }

    /**
     * @return array<int>
     */
    public function estatusPendientesAtencion(): array
    {
        return [
            EstatusCatalog::COTIZACION_SOLICITUD_RECIBIDA,
            EstatusCatalog::COTIZACION_EN_REVISION,
        ];
    }

    public function puedeMarcarEnRevision(Cotizacion $cotizacion): bool
    {
        return $this->motivoNoRevision($cotizacion) === null;
    }

    public function motivoNoRevision(Cotizacion $cotizacion): ?string
    {
        if ((int) $cotizacion->Id_Estatus !== EstatusCatalog::COTIZACION_SOLICITUD_RECIBIDA) {
            return 'Solo las solicitudes recibidas pueden pasar a revisión.';
        }

        return null;
    }

    public function marcarEnRevision(Cotizacion $cotizacion, ?string $comentario = null): Cotizacion
    {
        if ($this->motivoNoRevision($cotizacion) !== null) {
            throw ValidationException::withMessages([
                'cotizacion' => $this->motivoNoRevision($cotizacion),
            ]);
        }

        return DB::transaction(function () use ($cotizacion, $comentario) {
            $cotizacion->update([
                'Id_Estatus' => EstatusCatalog::COTIZACION_EN_REVISION,
            ]);

            CotizacionHistorial::create([
                'Id_Cotizacion' => $cotizacion->Id_Cotizacion,
                'Id_Estatus' => EstatusCatalog::COTIZACION_EN_REVISION,
                'Comentario' => $comentario ?: 'Solicitud en revisión por el equipo de la tienda.',
                'Fecha_Cambio' => now(),
            ]);

            return $cotizacion->fresh(['estatus', 'usuario', 'detalle.producto', 'historial.estatus']);
        });
    }

    public function contarPendientesAtencion(): int
    {
        return Cotizacion::query()
            ->pendientesAdmin($this->estatusPendientesAtencion())
            ->count();
    }

    public function puedeEmitir(Cotizacion $cotizacion): bool
    {
        return $this->motivoNoEmitir($cotizacion) === null;
    }

    public function motivoNoEmitir(Cotizacion $cotizacion): ?string
    {
        if ((int) $cotizacion->Id_Estatus !== EstatusCatalog::COTIZACION_EN_REVISION) {
            return 'Solo las solicitudes en revisión pueden emitirse como cotización formal.';
        }

        if (! $cotizacion->relationLoaded('detalle')) {
            $cotizacion->load('detalle');
        }

        if ($cotizacion->detalle->isEmpty()) {
            return 'La solicitud no tiene líneas de detalle para cotizar.';
        }

        return null;
    }

    /**
     * @param  array<string, array{costo_unit: mixed}>  $lineas
     */
    public function emitir(
        Cotizacion $cotizacion,
        array $lineas,
        string $terminos,
        int $vigenciaDias,
        UploadedFile $archivo,
        ?string $comentario = null
    ): Cotizacion {
        if ($this->motivoNoEmitir($cotizacion) !== null) {
            throw ValidationException::withMessages([
                'cotizacion' => $this->motivoNoEmitir($cotizacion),
            ]);
        }

        $cotizacion->load('detalle');

        $idsDetalle = $cotizacion->detalle->pluck('Id_CotizacionDetalle')->map(fn ($id) => (string) $id)->all();
        $idsEnviados = array_keys($lineas);

        if (array_diff($idsDetalle, $idsEnviados) !== [] || array_diff($idsEnviados, $idsDetalle) !== []) {
            throw ValidationException::withMessages([
                'lineas' => 'Debes indicar el precio de todas las líneas de la solicitud.',
            ]);
        }

        $subtotalCotizacion = 0.0;
        $actualizacionesLineas = [];

        foreach ($cotizacion->detalle as $detalle) {
            $clave = (string) $detalle->Id_CotizacionDetalle;
            $costoUnit = round((float) ($lineas[$clave]['costo_unit'] ?? 0), 2);
            $cantidad = (int) $detalle->Cantidad;
            $subtotalLinea = round($costoUnit * $cantidad, 2);
            $subtotalCotizacion += $subtotalLinea;

            $actualizacionesLineas[$detalle->Id_CotizacionDetalle] = [
                'Costo_Unit' => $costoUnit,
                'Subtotal' => $subtotalLinea,
            ];
        }

        $subtotalCotizacion = round($subtotalCotizacion, 2);

        return DB::transaction(function () use (
            $cotizacion,
            $actualizacionesLineas,
            $subtotalCotizacion,
            $terminos,
            $vigenciaDias,
            $archivo,
            $comentario
        ) {
            $rutaArchivo = $this->guardarArchivoEmitido($archivo, $cotizacion->Id_Cotizacion);

            foreach ($actualizacionesLineas as $idDetalle => $datos) {
                CotizacionDetalle::query()
                    ->where('Id_CotizacionDetalle', $idDetalle)
                    ->where('Id_Cotizacion', $cotizacion->Id_Cotizacion)
                    ->update($datos);
            }

            $cotizacion->update([
                'Cot_Subtotal' => $subtotalCotizacion,
                'Cot_Total' => $subtotalCotizacion,
                'Cot_Terminos' => $terminos,
                'Cot_VigenciaDias' => $vigenciaDias,
                'Cot_FechaEmision' => now(),
                'Cot_Archivo' => $rutaArchivo,
                'Id_Estatus' => EstatusCatalog::COTIZACION_EMITIDA,
            ]);

            CotizacionHistorial::create([
                'Id_Cotizacion' => $cotizacion->Id_Cotizacion,
                'Id_Estatus' => EstatusCatalog::COTIZACION_EMITIDA,
                'Comentario' => $comentario ?: 'Cotización formal emitida. El cliente puede descargar el documento y aceptar o rechazar antes de que venza el plazo.',
                'Fecha_Cambio' => now(),
            ]);

            return $cotizacion->fresh(['estatus', 'usuario', 'detalle.producto', 'historial.estatus']);
        });
    }

    private function guardarArchivoEmitido(UploadedFile $archivo, int $idCotizacion): string
    {
        $extension = strtolower($archivo->getClientOriginalExtension() ?: 'pdf');
        $nombreArchivo = Str::uuid()->toString().'.'.$extension;
        $directorio = 'cotizaciones-emitidas/'.$idCotizacion;

        $archivo->storeAs($directorio, $nombreArchivo, 'public');

        return $directorio.'/'.$nombreArchivo;
    }
}
