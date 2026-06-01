<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotizacionHistorial extends Model
{
    protected $table = 'tb_cotizacionhistorial';

    protected $primaryKey = 'Id_CotizacionHistorial';

    public $timestamps = false;

    protected $fillable = [
        'Id_Cotizacion',
        'Id_Estatus',
        'Comentario',
        'Fecha_Cambio',
    ];

    protected function casts(): array
    {
        return [
            'Fecha_Cambio' => 'datetime',
        ];
    }

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class, 'Id_Cotizacion', 'Id_Cotizacion');
    }

    public function estatus(): BelongsTo
    {
        return $this->belongsTo(Estatus::class, 'Id_Estatus', 'Id_Estatus');
    }
}
