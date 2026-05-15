<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdImagen extends Model
{
    protected $table = 'tb_prodimagen';
    protected $primarykey = 'Id_ProdImagen';
    public $timestamps = false;

    protected $fillable = [
        'Id_Producto',
        'url',
        'orden'
    ];

    public function producto(){
        // se usa belongsTo porque cada imagen pertenece a un producto específico
        return $this->belongsTo(Producto::class, 'Id_Producto', 'Id_Producto');
    }

}
