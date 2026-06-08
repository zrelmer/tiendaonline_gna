<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'tb_marca';

    protected $primaryKey = 'Id_Marca';

    public function getRouteKeyName(): string
    {
        return 'Id_Marca';
    }

    protected $fillable = [
        'Nom_Marca',
        'slug_Marca',
        'Descrip_Marca',
        'Marc_Logo',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'Id_Marca', 'Id_Marca');
    }

    public function scopeBuscarAdmin($query, string $termino)
    {
        $termino = trim($termino);

        if ($termino === '') {
            return $query;
        }

        $like = '%'.$termino.'%';

        return $query->where(function ($q) use ($termino, $like) {
            $q->where('Nom_Marca', 'like', $like)
                ->orWhere('slug_Marca', 'like', $like)
                ->orWhere('Descrip_Marca', 'like', $like);

            if (ctype_digit($termino)) {
                $q->orWhere('Id_Marca', (int) $termino);
            }
        });
    }
}
