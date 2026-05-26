<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'tb_categoria';
    protected $primarykey = 'Id_Categoria';
    // timestamps no se establece porque si se usara created_at y updated_at se tendría que agregar esas columnas a la tabla, pero como no se van a usar, se establece en false para evitar errores.

    protected $fillable = [
        'Cate_Nombre',
        'Cate_Slug',
        'Cate_Descripcion',
        // nueva columna para la imagen de la categoría
        'Cate_Imagen'
    ];

    public function productos(){
        return $this->hasMany(Producto::class, 'Id_Categoria', 'Id_Categoria');
    }
}
