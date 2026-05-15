<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $table = 'tb_carrito';
    protected $primarykey = 'Id_Carrito';
    // public $timestamps = false;
    // este protected $fillable define los campos que se pueden asignar masivamente, en este caso solo el Id_Usuario, ya que el carrito se crea automáticamente cuando un usuario se registra
    protected $fillable = [
        'Id_Usuario',
    ];
    // esta función define la relación entre el carrito y el usuario, indicando que cada carrito pertenece a un usuario específico
    public function usuario(){
        // se usa belongsTo porque cada carrito pertenece a un usuario específico
        return $this->belongsTo(Usuario::class, 'Id_Usuario', 'Id_Usuario');
    }
    // esta función define la relación entre el carrito y los detalles del carrito, indicando que un carrito puede tener múltiples detalles de carrito asociados a él
    public function detallescarrito(){
        // se usa hasMany porque un carrito puede tener múltiples detalles de carrito asociados a él
        return $this->hasMany(CarritoDetalle::class, 'Id_Carrito', 'Id_Carrito');
    }
}
