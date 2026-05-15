<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioHistorial extends Model
{
    protected $table = 'tb_inventariohistorial';
    protected $primarykey = 'Id_InventarioHistorial';

    public $timestamps = false;

    protected $fillable = [
            'Id_Inventario',
            'Id_Movimiento',
            'Cantidad',
            'Stock_Antes',
            'Stock_Despues',
            'Referencia',
            'Fecha_Movimiento',
    ];

    public function inventario(){
        // se utiliza belongsTo porque cada historial de movimiento pertenece a un inventario específico
        return $this->belongsTo(Inventario::class, 'Id_Inventario', 'Id_Inventario');
    }
    public function movimiento(){
        return $this->belongsTo(Movimiento::class, 'Id_Movimiento', 'Id_Movimiento');
    }



}