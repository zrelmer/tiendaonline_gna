<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarritoSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = DB::table('tb_usuario')->pluck('Id_Usuario');

        $data = [];

        foreach ($usuarios as $usuario) {
            $data[] = [
                'Id_Usuario' => $usuario,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('tb_carrito')->insert($data);
    }
}