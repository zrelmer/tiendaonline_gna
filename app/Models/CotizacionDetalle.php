<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotizacionDetalle extends Model
{
    protected $table = 'tb_cotizaciondetalle';

    protected $primaryKey = 'Id_CotizacionDetalle';

    public $timestamps = false;

    protected $fillable = [
        'Id_Cotizacion',
        'Id_Producto',
        'Cantidad',
        'Descripcion',
        'Costo_Unit',
        'Subtotal',
    ];

    protected function casts(): array
    {
        return [
            'Costo_Unit' => 'decimal:2',
            'Subtotal' => 'decimal:2',
        ];
    }

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class, 'Id_Cotizacion', 'Id_Cotizacion');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'Id_Producto', 'Id_Producto');
    }
}
