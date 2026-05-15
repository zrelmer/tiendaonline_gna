<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    // utilizamos protected par definir la tabla y la clave primaria y
    // public para definir los campos que se pueden llenar
    // protected es un modificador de acceso que permite que la propiedad sea accedida solo
    // dentro de la clase y sus subclases, pero no desde fuera de la clase. Esto es útil para proteger los datos sensibles y evitar que sean modificados accidentalmente desde fuera de la clase.
    protected $table = 'tb_rol';
    protected $primaryKey = 'Id_Rol';
    public $timestamps = false;

    // definimos los campos que se pueden llenar
    protected $fillable =[
         'Rol_Nombre',
    ];
    // creamos la funcion para relacionar con la tabla de usuarios
    public function usuarios(){
        // se usa hasMany porque un rol puede tener muchos usuarios asociados a él, y se especifica la clave foránea 'Id_Rol' en la tabla de usuarios y la clave primaria 'Id_Rol' en la tabla de roles para establecer la relación entre ambas tablas.
        return $this->hasMany(Usuario::class, 'Id_Rol', 'Id_Rol');
    }

}