<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarritoDetalle extends Model
{
    protected $table = 'tb_carritodetalle';
    protected $primaryKey = 'Id_CarritoDetalle';

    protected $fillable =[
        'Id_Carrito',
        'Id_Producto',
        'Cantidad',
        'Precio'
    ];

    public function carrito(){
        // se utiliza belongsTo porque cada detalle pertenece a un carrito específico
        return $this->belongsTo(Carrito::class, 'Id_Carrito', 'Id_Carrito');
    }
    public function producto(){
        // se utiliza belongsTo porque cada detalle pertenece a un producto específico
        return $this->belongsTo(Producto::class, 'Id_Producto', 'Id_Producto');
    }
}
