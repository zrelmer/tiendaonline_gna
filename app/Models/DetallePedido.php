<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'tb_detallepedido';
    protected $primaryKey = 'Id_DetallePedido';

    protected $fillable = [
        'Id_Pedido',
        'Id_Producto',
        'DetaPed_Cantidad',
        'DetaPed_Precio',
        'DetaPed_SubTotal',
    ];

    public function pedido(){
        // se utiliza belongsTo porque cada detalle de pedido pertenece a un pedido específico
        return $this->belongsTo(Pedido::class, 'Id_Pedido', 'Id_Pedido');
    }
    public function producto(){
        // se utiliza belongsTo porque cada detalle de pedido pertenece a un producto específico
        return $this->belongsTo(Producto::class, 'Id_Producto', 'Id_Producto');
    }
}