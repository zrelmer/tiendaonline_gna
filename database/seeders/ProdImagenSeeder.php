<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProdImagen;
use App\Models\Producto;
class ProdImagenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    ProdImagen::truncate();

    $productos = Producto::all();

    foreach ($productos as $index => $producto) {

        // p1.png → producto 1
        $numero = $index + 1;

        ProdImagen::create([
            'Id_Producto' => $producto->Id_Producto,
            'url' => 'storage/products/p' . $numero . '.png',
            'orden' => 1
        ]);
    }
}
}
