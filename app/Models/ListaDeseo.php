<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaDeseo extends Model
{
    protected $table = 'tb_listadeseo';
    protected $primaryKey = 'Id_ListaDeseo';
    public $timestamps = false;

    protected $fillable = [
        'Id_Usuario',
        'Id_Producto'
    ];

    public function usuario(){
        // se usa belongsTo porque cada elemento de la lista de deseos pertenece a un usuario específico
        return $this->belongsTo(Usuario::class, 'Id_Usuario', 'Id_Usuario');
    }
    public function producto(){
        // se usa belongsTo porque cada elemento de la lista de deseos pertenece a un producto específico
        return $this->belongsTo(Producto::class, 'Id_Producto', 'Id_Producto');
    }
}
