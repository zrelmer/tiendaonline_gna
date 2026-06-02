<?php

namespace App\Models;

use App\Support\RichText;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'tb_producto';
    // protected $primarykey = 'Id_Producto';
    protected $primaryKey = 'Id_Producto';

    public function getRouteKeyName(): string
    {
        return 'Id_Producto';
    }

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

    public function scopeBuscarAdmin($query, string $termino)
    {
        $termino = trim($termino);

        if ($termino === '') {
            return $query;
        }

        $like = '%'.$termino.'%';

        return $query->where(function ($q) use ($termino, $like) {
            $q->where('Prod_Nombre', 'like', $like)
                ->orWhere('Prod_Slug', 'like', $like)
                ->orWhereHas('categoria', fn ($c) => $c->where('Cate_Nombre', 'like', $like))
                ->orWhereHas('marca', fn ($m) => $m->where('Nom_Marca', 'like', $like))
                ->orWhereHas('estatus', fn ($e) => $e->where('Nom_Estatus', 'like', $like));

            if (ctype_digit($termino)) {
                $q->orWhere('Id_Producto', (int) $termino);
            }
        });
    }

    public function scopeFiltroInventarioAdmin($query, ?string $filtro, int $umbral)
    {
        if ($filtro === null) {
            return $query;
        }

        return match ($filtro) {
            \App\Services\AdminInventarioService::FILTRO_BAJO_STOCK => $query->whereHas(
                'inventario',
                fn ($inventario) => $inventario->whereRaw('(Stock - Stock_Reservado) <= ?', [$umbral])
            ),
            \App\Services\AdminInventarioService::FILTRO_SIN_INVENTARIO => $query->whereDoesntHave('inventario'),
            \App\Services\AdminInventarioService::FILTRO_SIN_STOCK => $query->where(function ($q) {
                $q->whereDoesntHave('inventario')
                    ->orWhereHas(
                        'inventario',
                        fn ($inventario) => $inventario->whereRaw('(Stock - Stock_Reservado) <= 0')
                    );
            }),
            \App\Services\AdminInventarioService::FILTRO_CON_STOCK => $query->whereHas(
                'inventario',
                fn ($inventario) => $inventario->whereRaw('(Stock - Stock_Reservado) > 0')
            ),
            default => $query,
        };
    }

    protected function descripcionSegura(): Attribute
    {
        return Attribute::get(
            fn () => RichText::forDisplay($this->Prod_Descripcion)
        );
    }

    protected function descripcionResumen(): Attribute
    {
        return Attribute::get(
            fn () => RichText::toPlainText($this->Prod_Descripcion)
        );
    }
}
