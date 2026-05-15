<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PedidoSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = DB::table('tb_usuario')->pluck('Id_Usuario');
        $direcciones = DB::table('tb_direccion')->pluck('Id_Direccion');

        $data = [];

        foreach ($usuarios as $usuario) {
            if (rand(0, 1)) {

                $data[] = [
                    'Id_Usuario' => $usuario,
                    'Id_Direccion' => $direcciones->random(),
                    'Ped_Numero' => 'ORD-' . strtoupper(Str::random(8)),
                    'Ped_TotalPrecio' => rand(100, 2000),
                    'Id_Estatus' => rand(1, 4),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('tb_pedido')->insert($data);
    }
}
