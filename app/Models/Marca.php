<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'tb_marca';
    protected $primarikey = 'Id_Marca';
    // public $timestamps = false;

    protected $fillable = [
        'Nom_Marca',
        'Slug_Marca',
        'Descrip_Marca',
    ];

    public function productos(){
        return $this->hasMany(Producto::class, 'Id_Marca', 'Id_Marca');
    }
}
