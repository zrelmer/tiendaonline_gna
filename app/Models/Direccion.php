<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $table = 'tb_direccion';
    protected $primarykey = 'Id_Direccion';
    public $timestamps = false;

    protected $fillable = [
        'Id_Usuario',
        'Direccion',
        'Id_Municipio'
    ];

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'Id_Usuario', 'Id_Usuario');
    }
    public function municipio(){
        return $this->belongsTo(Municipio::class, 'Id_Municipio', 'Id_Municipio');
    }

    public function pedido(){
        return $this->hasMany(Pedido::class, 'Id_Direccion', 'Id_Direccion');
    }
}