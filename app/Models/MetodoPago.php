<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'tb_metodopago';
    protected $primarykey = 'Id_MetodoPago';
    public $timestamps = false;

    protected $fillable = [
        'MetPag_Descripcion',
    ];

    public function pagos(){
        return $this->hasMany(Pago::class, 'Id_MetodoPago', 'Id_MetodoPago');
    }

}