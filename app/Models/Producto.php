<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'tb_producto';
    // protected $primarykey = 'Id_Producto';
    protected $primaryKey = 'Id_Producto';
    // public $timestamps = false;

    protected $fillable = [
        'Id_Categoria',
        'Id_Marca',
        'Prod_Nombre',
        'Prod_Slug',
        'Prod_Descripcion',
        'Prod_Precio',
        'Prod_PrecioOferta',
        'Id_Estatus',
        'Prod_Activo'
    ];

    public function categoria(){
        // se usa belongsTo porque cada producto pertenece a una categoría específica
        return $this->belongsTo(Categoria::class, 'Id_Categoria', 'Id_Categoria');
    }
    public function marca(){
        // se usa belongsTo porque cada producto pertenece a una marca específica
        return $this->belongsTo(Marca::class , 'Id_Marca', 'Id_Marca');
    }
    public function estatus(){
        // se usa belongsTo porque cada producto tiene un estatus específico
        return $this->belongsTo(Estatus::class, 'Id_Estatus', 'Id_Estatus');
    }
    public function imagenes()
    {
        return $this->hasMany(ProdImagen::class, 'Id_Producto', 'Id_Producto');
    }
    public function comentarios(){
        // se usa hasMany porque un producto puede tener múltiples comentarios asociados a él
        return $this->hasMany(Comentario::class, 'Id_Producto', 'Id_Producto');
    }

    public function listadeseos(){
        // se usa hasMany porque un producto puede estar en la lista de deseos de múltiples usuarios
        return $this->hasMany(ListaDeseo::class, 'Id_Producto', 'Id_Producto');
    }

    public function carritodetalles(){
        return $this->hasMany(CarritoDetalle::class, 'Id_Producto', 'Id_Producto');
    }

    public function inventario(){
        return $this->hasOne(Inventario::class, 'Id_Producto', 'Id_Producto');
    }

    public function detallepedidos(){
        return $this->hasMany(DetallePedido::class, 'Id_Producto', 'Id_Producto');
    }
}
