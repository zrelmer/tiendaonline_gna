<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tb_departamento')->insert([
            ['Id_Departamento' => 1, 'Nom_Departamento' => 'Guatemala'],
            ['Id_Departamento' => 2, 'Nom_Departamento' => 'Quetzaltenango'],
            ['Id_Departamento' => 3, 'Nom_Departamento' => 'Sacatepéquez'],
            ['Id_Departamento' => 4, 'Nom_Departamento' => 'Escuintla'],
            ['Id_Departamento' => 5, 'Nom_Departamento' => 'Petén'],
        ]);
    }
}
