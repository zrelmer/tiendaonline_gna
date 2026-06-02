<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioHistorial extends Model
{
    protected $table = 'tb_inventariohistorial';

    protected $primaryKey = 'Id_InventarioHistorial';

    public $timestamps = false;

    protected $fillable = [
        'Id_Inventario',
        'Id_Movimiento',
        'Cantidad',
        'Stock_Antes',
        'Stock_Despues',
        'Referencia',
        'Fecha_Movimiento',
    ];

    protected function casts(): array
    {
        return [
            'Fecha_Movimiento' => 'datetime',
        ];
    }

    public function scopeBuscarAdmin($query, string $termino)
    {
        $termino = trim($termino);

        if ($termino === '') {
            return $query;
        }

        $like = '%'.$termino.'%';

        return $query->where(function ($q) use ($termino, $like) {
            $q->where('Referencia', 'like', $like)
                ->orWhereHas('inventario.producto', function ($producto) use ($like) {
                    $producto->where('Prod_Nombre', 'like', $like)
                        ->orWhere('Prod_Slug', 'like', $like);
                })
                ->orWhereHas('movimiento', fn ($movimiento) => $movimiento->where('Nom_Movimiento', 'like', $like));

            if (ctype_digit($termino)) {
                $id = (int) $termino;
                $q->orWhere('Id_InventarioHistorial', $id)
                    ->orWhereHas('inventario', fn ($inventario) => $inventario->where('Id_Producto', $id))
                    ->orWhere('Referencia', 'like', '%PEDIDO:'.$id.'%');
            }
        });
    }

    public function scopeFiltroMovimientoAdmin($query, ?int $idMovimiento)
    {
        if ($idMovimiento === null || $idMovimiento <= 0) {
            return $query;
        }

        return $query->where('Id_Movimiento', $idMovimiento);
    }

    public function scopeFiltroProductoAdmin($query, ?int $idProducto)
    {
        if ($idProducto === null || $idProducto <= 0) {
            return $query;
        }

        return $query->whereHas(
            'inventario',
            fn ($inventario) => $inventario->where('Id_Producto', $idProducto)
        );
    }

    public function scopeFiltroFechasAdmin($query, ?string $desde, ?string $hasta)
    {
        if ($desde !== null && $desde !== '') {
            $query->whereDate('Fecha_Movimiento', '>=', $desde);
        }

        if ($hasta !== null && $hasta !== '') {
            $query->whereDate('Fecha_Movimiento', '<=', $hasta);
        }

        return $query;
    }

    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class, 'Id_Inventario', 'Id_Inventario');
    }

    public function movimiento(): BelongsTo
    {
        return $this->belongsTo(Movimiento::class, 'Id_Movimiento', 'Id_Movimiento');
    }
}