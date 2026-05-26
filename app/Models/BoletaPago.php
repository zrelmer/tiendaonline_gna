<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoletaPago extends Model
{
    protected $table = 'tb_boletapago';

    protected $primaryKey = 'Id_Boletapago';

    protected $fillable = [
        'BoletaImagen',
        'Id_Pedido',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'Id_Pedido', 'Id_Pedido');
    }
}
