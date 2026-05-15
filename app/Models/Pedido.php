<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'tb_pedido';
    protected $primarykey = 'Id_Pedido';

    protected $fillable = [
        'Id_Usuario',
        'Id_Direccion',
        'Ped_Numero',
        'Ped_TotalPrecio',
        'Id_Estatus',
    ];

    public function usuario(){
        // se utiliza belongsTo porque cada pedido pertenece a un usuario específico
        return $this->belongsTo(Usuario::class,'Id_Usuario','Id_Usuario');
    }
    public function direccion(){
        // se utiliza belongsTo porque cada pedido pertenece a una dirección específica
        return $this->belongsTo(Direccion::class,'Id_Direccion','Id_Direccion');
    }
    public function estatus(){
        // se utiliza belongsTo porque cada pedido tiene un estatus específico
        return $this->belongsTo(Estatus::class,'Id_Estatus','Id_Estatus');
    }
    public function detalle(){
        // se utiliza hasMany porque un pedido puede tener múltiples detalles de pedido
        return $this->hasMany(DetallePedido::class,'Id_Pedido','Id_Pedido');
    }
    public function historial(){
        return $this->hasMany(PedidoHistorial::class,'Id_Pedido','Id_Pedido');
    }
    public function pago(){
        return $this->hasOne(Pago::class,'Id_Pedido','Id_Pedido');
    }
    public function envio(){
        return $this->hasOne(Envio::class,'Id_Pedido','Id_Pedido');
    }
}