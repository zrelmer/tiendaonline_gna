<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoHistorial extends Model
{
    protected $table = 'tb_pedidohistorial';
    protected $primaryKey = 'Id_PedidoHistorial';
    public $timestamps = false;

    protected $fillable =[
        // 'Id_PedidoHistorial',
        'Id_Pedido',
        'Id_Estatus',
        'Comentario',
        'Fecha_Cambio'
    ];

    public function pedido(){
        return $this->belongsTo(Pedido::class, 'Id_Pedido', 'Id_Pedido');
    }
    public function estatus(){
        return $this->belongsTo(Estatus::class, 'Id_Estatus', 'Id_Estatus');
    }

    public function scopeBuscarAdmin($query, string $termino)
    {
        $termino = trim($termino);

        if ($termino === '') {
            return $query;
        }

        $like = '%'.$termino.'%';

        return $query->where(function ($q) use ($termino, $like) {
            $q->where('Comentario', 'like', $like)
                ->orWhereHas('estatus', fn ($estatus) => $estatus->where('Nom_Estatus', 'like', $like))
                ->orWhereHas('pedido', function ($pedido) use ($like, $termino) {
                    $pedido->where('Ped_Numero', 'like', $like);

                    if (ctype_digit($termino)) {
                        $pedido->orWhere('Id_Pedido', (int) $termino);
                    }

                    $pedido->orWhereHas('usuario', function ($usuario) use ($like) {
                        $usuario->where('Usu_Nombre', 'like', $like)
                            ->orWhere('Usu_Correo', 'like', $like);
                    });
                });

            if (ctype_digit($termino)) {
                $q->orWhere('Id_PedidoHistorial', (int) $termino);
            }
        });
    }
}