<?php

namespace Tests\Concerns;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Support\EstatusCatalog;
use Database\Seeders\EstatusSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Support\Str;

trait SeedsCatalog
{
    protected function seedRolesAndStatuses(): void
    {
        $this->seed([
            RolSeeder::class,
            EstatusSeeder::class,
        ]);
    }

    protected function createCategoria(array $attributes = []): Categoria
    {
        return Categoria::query()->create(array_merge([
            'Cate_Nombre' => 'Categoría prueba',
            'Cate_Slug' => 'categoria-prueba-'.Str::random(6),
            'Cate_Descripcion' => 'Descripción de prueba',
        ], $attributes));
    }

    protected function createMarca(array $attributes = []): Marca
    {
        return Marca::query()->create(array_merge([
            'Nom_Marca' => 'Marca prueba',
            'slug_Marca' => 'marca-prueba-'.Str::random(6),
            'Descrip_Marca' => 'Marca de prueba',
        ], $attributes));
    }

    protected function createProducto(?Categoria $categoria = null, ?Marca $marca = null, array $attributes = []): Producto
    {
        $categoria ??= $this->createCategoria();
        $marca ??= $this->createMarca();

        return Producto::query()->create(array_merge([
            'Id_Categoria' => $categoria->Id_Categoria,
            'Id_Marca' => $marca->Id_Marca,
            'Prod_Nombre' => 'Producto prueba',
            'Prod_Slug' => 'producto-prueba-'.Str::random(6),
            'Prod_Descripcion' => 'Descripción producto prueba',
            'Prod_Precio' => 100.00,
            'Prod_PrecioOferta' => null,
            'Id_Estatus' => EstatusCatalog::PRODUCTO_ACTIVO,
            'Prod_Activo' => true,
        ], $attributes));
    }
}
