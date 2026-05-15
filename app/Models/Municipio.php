<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'tb_municipio';
    protected $primarykey = 'Id_Municipio';
    public $timestamps = false;

    protected $fillable =[
        'Nom_Municipio',
        'Id_Departamento'
    ];

    public function departamentos(){
        // se usa belongsTo porque cada municipio pertenece a un departamento específico
        return $this->belongsTo(Departamento::class, 'Id_Departamento', 'Id_Departamento');
    }

    public function direcciones(){
        // se usa hasMany porque un municipio puede tener múltiples direcciones asociadas a él
        return $this->hasMany(Direccion::class, 'Id_Municipio', 'Id_Municipio');
    }
}
