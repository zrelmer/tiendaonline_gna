<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'tb_pago';
    protected $primaryKey = 'Id_Pago';

    protected $fillable = [
        'Id_Pedido',
        'Id_MetodoPago',
        'Transaccion_Id',
        'Transaccion_Json',
        'Id_Estatus',
    ];

    // esto permite que Laravel convierta automáticamente el campo Transaccion_Json a un array cuando se accede a él, y lo convierta de nuevo a JSON cuando se guarda en la base de datos
    protected $casts = [
        'Transaccion_Json' => 'array',
    ];

    public function pedido(){
        // se utiliza belongsTo porque cada pago pertenece a un pedido específico
        return $this->belongsTo(Pedido::class, 'Id_Pedido', 'Id_Pedido');
    }
    public function metodoPago(){
        // se utiliza belongsTo porque cada pago pertenece a un método de pago específico
        return $this->belongsTo(MetodoPago::class, 'Id_MetodoPago', 'Id_MetodoPago');
    }
    public function estatus(){
        // se utiliza belongsTo porque cada pago tiene un estatus específico
        return $this->belongsTo(Estatus::class, 'Id_Estatus', 'Id_Estatus');
    }

}