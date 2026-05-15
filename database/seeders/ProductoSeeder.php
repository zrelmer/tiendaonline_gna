<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductoSeeder extends Seeder
{
    public function run(): void
{
    $faker = Faker::create();

    $productos = [];

    for ($i = 1; $i <= 31; $i++) {

        $nombre = $faker->words(3, true);
        $slug = strtolower(str_replace(' ', '-', $nombre));

        $precio = $faker->numberBetween(100, 2000);

        $productos[] = [
            'Id_Categoria' => $faker->numberBetween(1, 3),
            'Id_Marca' => $faker->numberBetween(1, 5),
            'Prod_Nombre' => ucfirst($nombre),
            'Prod_Slug' => $slug . '-' . $i,
            'Prod_Descripcion' => $faker->paragraph,
            'Prod_Precio' => $precio,
            'Prod_PrecioOferta' => $faker->optional()->numberBetween(80, $precio),
            'Id_Estatus' => 1,
            'Prod_Activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('tb_producto')->insert($productos);
}
}