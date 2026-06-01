<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'tb_departamento';

    protected $primaryKey = 'Id_Departamento';

    public $timestamps = false;

    public function getRouteKeyName(): string
    {
        return 'Id_Departamento';
    }

    protected $fillable = [
        'Nom_Departamento',
    ];

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'Id_Departamento', 'Id_Departamento');
    }

    public function scopeBuscarAdmin($query, string $termino)
    {
        $termino = trim($termino);

        if ($termino === '') {
            return $query;
        }

        $like = '%'.$termino.'%';

        return $query->where(function ($q) use ($termino, $like) {
            $q->where('Nom_Departamento', 'like', $like);

            if (ctype_digit($termino)) {
                $q->orWhere('Id_Departamento', (int) $termino);
            }
        });
    }
}
