<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'tb_municipio';

    protected $primaryKey = 'Id_Municipio';

    public $timestamps = false;

    public function getRouteKeyName(): string
    {
        return 'Id_Municipio';
    }

    protected $fillable = [
        'Nom_Municipio',
        'Id_Departamento',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'Id_Departamento', 'Id_Departamento');
    }

    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'Id_Municipio', 'Id_Municipio');
    }

    public function scopeBuscarAdmin($query, string $termino)
    {
        $termino = trim($termino);

        if ($termino === '') {
            return $query;
        }

        $like = '%'.$termino.'%';

        return $query->where(function ($q) use ($termino, $like) {
            $q->where('Nom_Municipio', 'like', $like)
                ->orWhereHas('departamento', fn ($dep) => $dep->where('Nom_Departamento', 'like', $like));

            if (ctype_digit($termino)) {
                $q->orWhere('Id_Municipio', (int) $termino);
            }
        });
    }
}
