<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tb_estatus')->insert([
            [
                'Id_Estatus' => 1,
                'Nom_Estatus' => 'Activo',
            ],
            [
                'Id_Estatus' => 2,
                'Nom_Estatus' => 'Inactivo',
            ],
            [
                'Id_Estatus' => 3,
                'Nom_Estatus' => 'Agotado',
            ],
            [
                'Id_Estatus' => 4,
                'Nom_Estatus' => 'Pendiente',
            ],
        ]);
    }
}
