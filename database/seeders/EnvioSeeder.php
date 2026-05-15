<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnvioSeeder extends Seeder
{
    public function run(): void
    {
        $pedidos = DB::table('tb_pedido')->get();

        $data = [];

        foreach ($pedidos as $pedido) {
            $data[] = [
                'Id_Pedido' => $pedido->Id_Pedido,
                'Direccion_Envio' => 'Dirección de envío simulada',
                'Empresa_Envio' => 'Guatex',
                'Numero_Guia' => 'GUIA-' . rand(10000, 99999),
                'Fecha_Envio' => now(),
                'Fecha_Entrega' => now()->addDays(3),
                'Id_Estatus' => 1,
            ];
        }

        DB::table('tb_envio')->insert($data);
    }
}