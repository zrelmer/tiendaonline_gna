<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PedidoHistorialSeeder extends Seeder
{
    public function run(): void
    {
        $pedidos = DB::table('tb_pedido')->get();

        $data = [];

        foreach ($pedidos as $pedido) {
            $data[] = [
                'Id_Pedido' => $pedido->Id_Pedido,
                'Id_Estatus' => $pedido->Id_Estatus,
                'Comentario' => 'Estado inicial del pedido',
                'Fecha_Cambio' => now(),
            ];
        }

        DB::table('tb_pedidohistorial')->insert($data);
    }
}
