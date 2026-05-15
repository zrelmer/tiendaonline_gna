<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarritoDetalleSeeder extends Seeder
{
    public function run(): void
    {
        $carritos = DB::table('tb_carrito')->get();
        $productos = DB::table('tb_producto')->pluck('Id_Producto')->toArray();

        $data = [];

        foreach ($carritos as $carrito) {

            $items = rand(1, 3);

            $productosRandom = array_rand($productos, min($items, count($productos)));

            foreach ((array)$productosRandom as $index) {
                $productoId = $productos[$index];

                $precio = DB::table('tb_producto')
                    ->where('Id_Producto', $productoId)
                    ->value('Prod_Precio');

                $data[] = [
                    'Id_Carrito' => $carrito->Id_Carrito,
                    'Id_Producto' => $productoId,
                    'Cantidad' => rand(1, 3),
                    'Precio' => $precio,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('tb_carritodetalle')->insert($data);
    }
}