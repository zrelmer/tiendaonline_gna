<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
