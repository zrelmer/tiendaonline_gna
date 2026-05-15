<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'tb_inventario';
    protected $primarykey = 'Id_Inventario';

    protected $fillable =[
        'Id_Producto',
        'Stock',
        'Stock_Reservado'
    ];

    public function producto(){
        // se utiliza belongsTo porque cada inventario pertenece a un producto específico
        return $this->belongsTo(Producto::class, 'Id_Producto', 'Id_Producto');
    }
   public function historial(){
        // se utiliza hasMany porque un inventario puede tener múltiples registros en el historial de movimientos
        return $this->hasMany(InventarioHistorial::class, 'Id_Inventario', 'Id_Inventario');
   }
}
