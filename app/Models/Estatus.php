<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
    protected $table = 'tb_estatus';
    protected $primarykey = 'Id_Estatus';
    public $timestamps = false;

    protected $fillable = [
        'Nom_Estatus'
    ] ;

    // relación con el modelo Producto
    public function productos(){
        return $this->hasMany(Producto::class, 'Id_Estatus','Id_Estatus');
    }
    // relacion con el modelo pedidos
    public function pedidos(){
        return $this->hasMany(Pedido::class, 'Id_Estatus','Id_Estatus');
    }
    // relacion con el modelo pago
    public function pagos(){
        return $this->hasMany(Pago::class, 'Id_Estatus','Id_Estatus');
    }
    // relacion con el modelo envio
    public function envios(){
        return $this->hasMany(Envio::class, 'Id_Estatus','Id_Estatus');
    }

    // relciaon con el modelo paedidohistorial
    public function pedidoHistoriales(){
        return $this->hasMany(PedidoHistorial::class, 'Id_Estatus','Id_Estatus');
    }
}
