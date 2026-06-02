<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $table = 'tb_movimiento';

    protected $primaryKey = 'Id_Movimiento';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'Nom_Movimiento',
    ];

    public function inventarioHistoriales(){
        return $this->hasMany(InventarioHistorial::class, 'Id_Movimiento', 'Id_Movimiento');
    }
}
