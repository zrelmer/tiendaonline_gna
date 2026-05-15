<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoHistorial extends Model
{
    protected $table = 'tb_pedidohistorial';
    protected $primarykey = 'Id_PedidoHistorial';
    public $timestamps = false;

    protected $fillable =[
        // 'Id_PedidoHistorial',
        'Id_Pedido',
        'Id_Estatus',
        'Comentario',
        'Fecha_Cambio'
    ];

    public function pedido(){
        return $this->belongsTo(Pedido::class, 'Id_Pedido', 'Id_Pedido');
    }
    public function estatus(){
        return $this->belongsTo(Estatus::class, 'Id_Estatus', 'Id_Estatus');
    }

}