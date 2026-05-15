<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tb_rol')->insert([
            [
                'Id_Rol' => 1,
                'Rol_Nombre' => 'Administrador',
            ],
            [
                'Id_Rol' => 2,
                'Rol_Nombre' => 'Usuario',
            ],
        ]);
    }
}
