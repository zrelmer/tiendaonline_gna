<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'tb_pedido';

    protected $primaryKey = 'Id_Pedido';

    protected $fillable = [
        'Id_Usuario',
        'Id_Direccion',
        'Ped_Numero',
        'Ped_TotalPrecio',
        'Id_Estatus',
        'Ped_OcultoAdmin',
    ];

    protected function casts(): array
    {
        return [
            'Ped_OcultoAdmin' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'Id_Pedido';
    }

    public function usuario(){
        // se utiliza belongsTo porque cada pedido pertenece a un usuario específico
        return $this->belongsTo(Usuario::class,'Id_Usuario','Id_Usuario');
    }
    public function direccion(){
        // se utiliza belongsTo porque cada pedido pertenece a una dirección específica
        return $this->belongsTo(Direccion::class,'Id_Direccion','Id_Direccion');
    }
    public function estatus(){
        // se utiliza belongsTo porque cada pedido tiene un estatus específico
        return $this->belongsTo(Estatus::class,'Id_Estatus','Id_Estatus');
    }
    public function detalle(){
        // se utiliza hasMany porque un pedido puede tener múltiples detalles de pedido
        return $this->hasMany(DetallePedido::class,'Id_Pedido','Id_Pedido');
    }
    public function historial(){
        return $this->hasMany(PedidoHistorial::class,'Id_Pedido','Id_Pedido');
    }
    public function pago(){
        return $this->hasOne(Pago::class,'Id_Pedido','Id_Pedido');
    }
    public function envio(){
        return $this->hasOne(Envio::class,'Id_Pedido','Id_Pedido');
    }

    public function boletaPago()
    {
        return $this->hasOne(BoletaPago::class, 'Id_Pedido', 'Id_Pedido');
    }

    public function scopeVisibleEnAdmin($query)
    {
        return $query->where('Ped_OcultoAdmin', false);
    }

    /**
     * @param  array<int>  $estatusIds
     */
    public function scopePendientesSeguimientoAdmin($query, array $estatusIds)
    {
        return $query
            ->visibleEnAdmin()
            ->whereIn('Id_Estatus', $estatusIds);
    }

    public function scopeBuscarAdmin($query, string $termino)
    {
        $termino = trim($termino);

        if ($termino === '') {
            return $query;
        }

        $like = '%'.$termino.'%';

        return $query->where(function ($q) use ($termino, $like) {
            $q->where('Ped_Numero', 'like', $like)
                ->orWhereHas('usuario', function ($usuario) use ($like) {
                    $usuario->where('Usu_Nombre', 'like', $like)
                        ->orWhere('Usu_Correo', 'like', $like);
                })
                ->orWhereHas('estatus', fn ($estatus) => $estatus->where('Nom_Estatus', 'like', $like))
                ->orWhereHas('pago.metodoPago', fn ($metodo) => $metodo->where('MetPag_Descripcion', 'like', $like))
                ->orWhereHas('pago.estatus', fn ($estatus) => $estatus->where('Nom_Estatus', 'like', $like));

            if (ctype_digit($termino)) {
                $q->orWhere('Id_Pedido', (int) $termino);
            }
        });
    }
}