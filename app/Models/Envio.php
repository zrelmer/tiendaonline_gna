<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    protected $table = 'tb_envio';
    protected $primarykey = 'Id_Envio';

    protected $fillable = [
        'Id_Pedido',
        'Direccion_Envio',
        'Empresa_Envio',
        'Numero_Guia',
        'Fecha_Envio',
        'Fecha_Entrega',
        'Id_Estatus'
    ];

    public function pedido(){
        return $this->belongsTo(Pedido::class,'Id_Pedido','Id_Pedido');
    }
    public function estatus(){
        return $this->belongsTo(Estatus::class,'Id_Estatus','Id_Estatus');
    }

}