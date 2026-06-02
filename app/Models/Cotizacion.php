<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Cotizacion extends Model
{
    protected $table = 'tb_cotizacion';

    protected $primaryKey = 'Id_Cotizacion';

    protected $fillable = [
        'Id_Usuario',
        'Cot_Numero',
        'Cot_NombreCliente',
        'Cot_Nit',
        'Cot_Direccion',
        'Cot_Email',
        'Cot_NotasSolicitud',
        'Cot_Subtotal',
        'Cot_Total',
        'Cot_VigenciaDias',
        'Cot_Terminos',
        'Cot_FechaEmision',
        'Cot_Archivo',
        'Id_Estatus',
    ];

    protected function casts(): array
    {
        return [
            'Cot_FechaEmision' => 'datetime',
            'Cot_Subtotal' => 'decimal:2',
            'Cot_Total' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'Id_Cotizacion';
    }

    public function scopeBuscarAdmin($query, string $termino)
    {
        $termino = trim($termino);

        if ($termino === '') {
            return $query;
        }

        $like = '%'.$termino.'%';

        return $query->where(function ($q) use ($termino, $like) {
            $q->where('Cot_Numero', 'like', $like)
                ->orWhere('Cot_NombreCliente', 'like', $like)
                ->orWhere('Cot_Nit', 'like', $like)
                ->orWhere('Cot_Email', 'like', $like)
                ->orWhereHas('usuario', function ($usuario) use ($like) {
                    $usuario->where('Usu_Nombre', 'like', $like)
                        ->orWhere('Usu_Correo', 'like', $like);
                })
                ->orWhereHas('estatus', fn ($estatus) => $estatus->where('Nom_Estatus', 'like', $like));

            if (ctype_digit($termino)) {
                $q->orWhere('Id_Cotizacion', (int) $termino);
            }
        });
    }

    /**
     * @param  array<int>  $estatusIds
     */
    public function scopePendientesAdmin($query, array $estatusIds)
    {
        return $query->whereIn('Id_Estatus', $estatusIds);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'Id_Usuario', 'Id_Usuario');
    }

    public function estatus(): BelongsTo
    {
        return $this->belongsTo(Estatus::class, 'Id_Estatus', 'Id_Estatus');
    }

    public function detalle(): HasMany
    {
        return $this->hasMany(CotizacionDetalle::class, 'Id_Cotizacion', 'Id_Cotizacion');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(CotizacionHistorial::class, 'Id_Cotizacion', 'Id_Cotizacion');
    }

    public function archivoDisponible(): bool
    {
        return $this->Cot_Archivo !== null
            && $this->Cot_Archivo !== ''
            && Storage::disk('public')->exists($this->Cot_Archivo);
    }

    public function nombreArchivoDescarga(): string
    {
        $extension = strtolower(pathinfo((string) $this->Cot_Archivo, PATHINFO_EXTENSION) ?: 'pdf');

        return 'cotizacion-'.($this->Cot_Numero ?: $this->Id_Cotizacion).'.'.$extension;
    }
}
