<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'tb_comentario';
    // Cambio: Laravel requiere $primaryKey (K mayúscula) para usar la PK personalizada.
    protected $primaryKey = 'Id_Comentario';
    // Cambio: habilita created_at/updated_at para guardar fecha real de reseñas.
    public $timestamps = true;

    protected $fillable = [
        'Id_Usuario',
        'Id_Producto',
        'Rating',
        'Comentario'
    ];

    public function usuario(){
        // se usa belongsTo porque cada comentario pertenece a un usuario específico
        return $this->belongsTo(Usuario::class, 'Id_Usuario', 'Id_Usuario');
    }
    public function producto(){
        // se usa belongsTo porque cada comentario pertenece a un producto específico
        return $this->belongsTo(Producto::class, 'Id_Producto', 'Id_Producto');
    }
}