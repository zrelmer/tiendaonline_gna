<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagoSeeder extends Seeder
{
    public function run(): void
    {
        $pedidos = DB::table('tb_pedido')->get();

        $data = [];

        foreach ($pedidos as $pedido) {
            $data[] = [
                'Id_Pedido' => $pedido->Id_Pedido,
                'Id_MetodoPago' => rand(1, 3),
                'Transaccion_id' => uniqid('TXN-'),
                'Transaccion_json' => json_encode(['estado' => 'pagado']),
                'Id_Estatus' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('tb_pago')->insert($data);
    }
}
