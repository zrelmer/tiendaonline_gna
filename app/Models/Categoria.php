<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'tb_categoria';
    protected $primaryKey = 'Id_Categoria';

    public function getRouteKeyName(): string
    {
        return 'Id_Categoria';
    }

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

    public function scopeBuscarAdmin($query, string $termino)
    {
        $termino = trim($termino);

        if ($termino === '') {
            return $query;
        }

        $like = '%'.$termino.'%';

        return $query->where(function ($q) use ($termino, $like) {
            $q->where('Cate_Nombre', 'like', $like)
                ->orWhere('Cate_Slug', 'like', $like)
                ->orWhere('Cate_Descripcion', 'like', $like);

            if (ctype_digit($termino)) {
                $q->orWhere('Id_Categoria', (int) $termino);
            }
        });
    }
}
