<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetallePedidoSeeder extends Seeder
{
    public function run(): void
    {
        $pedidos = DB::table('tb_pedido')->get();
        $productos = DB::table('tb_producto')->pluck('Id_Producto')->toArray();

        $data = [];

        foreach ($pedidos as $pedido) {

            $items = rand(1, 3);

            $productosRandom = array_rand($productos, min($items, count($productos)));

            foreach ((array)$productosRandom as $index) {

                $productoId = $productos[$index];

                $precio = DB::table('tb_producto')
                    ->where('Id_Producto', $productoId)
                    ->value('Prod_Precio');

                $cantidad = rand(1, 2);

                $data[] = [
                    'Id_Pedido' => $pedido->Id_Pedido,
                    'Id_Producto' => $productoId,
                    'DetaPed_Cantidad' => $cantidad,
                    'DetaPed_Precio' => $precio,
                    'DetaPed_SubTotal' => $precio * $cantidad,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('tb_detallepedido')->insert($data);
    }
}