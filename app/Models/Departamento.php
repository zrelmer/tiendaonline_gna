<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// class Departamento extends Model
class Departamento extends Model
{
    // este protected $table se utiliza para especificar el nombre de la tabla en la base de datos que corresponde a este modelo. En este caso, se indica que el modelo Departamento está asociado con la tabla 'tb_departamento'.
    protected $table = 'tb_departamento';
    // El protected $primaryKey se utiliza para especificar el nombre de la columna que actúa como clave primaria en la tabla. En este caso, se indica que la clave primaria es 'Id_Departamento'.
    protected $primaryKey = 'Id_Departamento';
    // El public $timestamps se establece en false para indicar que este modelo no utilizará las columnas de marca de tiempo (created_at y updated_at) que Laravel agrega automáticamente a las tablas. Esto es útil cuando la tabla no tiene estas columnas o cuando no se desea que Laravel las maneje.
    public $timestamps = false;
    // El protected $fillable es un arreglo que define qué atributos del modelo pueden ser asignados masivamente (mass assignable). Esto es una medida de seguridad para evitar la asignación no intencionada de atributos. En este caso, se permite la asignación masiva del atributo 'Departamento_Nombre'.
    protected $fillable = [
        'Departamento_Nombre',
    ];
    // El public function municipios() define una relación entre el modelo Departamento y el modelo Municipio. En este caso, se establece que un departamento puede tener muchos municipios. La función utiliza el método hasMany para indicar que la relación es de uno a muchos, donde el modelo Municipio tiene una clave foránea 'Id_Departamento' que se relaciona con la clave primaria 'Id_Departamento' del modelo Departamento.
    public function municipios(){
        return $this->hasMany(Municipio::class, 'Id_Departamento', 'Id_Departamento');
    }

}