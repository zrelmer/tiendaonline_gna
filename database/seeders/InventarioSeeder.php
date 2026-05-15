<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        $productos = DB::table('tb_producto')->get();

        $data = [];

        foreach ($productos as $producto) {
            $stock = rand(10, 100);
            $reservado = rand(0, 5);

            $data[] = [
                'Id_Producto' => $producto->Id_Producto,
                'Stock' => $stock,
                'Stock_Reservado' => $reservado,
                'Ultima_Actualizacion' => now(),
            ];
        }

        DB::table('tb_inventario')->insert($data);
    }
}