<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tb_municipio')->insert([
            // Guatemala
            ['Nom_Municipio' => 'Ciudad de Guatemala', 'Id_Departamento' => 1],
            ['Nom_Municipio' => 'Mixco', 'Id_Departamento' => 1],
            ['Nom_Municipio' => 'Villa Nueva', 'Id_Departamento' => 1],

            // Quetzaltenango
            ['Nom_Municipio' => 'Quetzaltenango', 'Id_Departamento' => 2],
            ['Nom_Municipio' => 'Olintepeque', 'Id_Departamento' => 2],

            // Sacatepéquez
            ['Nom_Municipio' => 'Antigua Guatemala', 'Id_Departamento' => 3],
            ['Nom_Municipio' => 'Ciudad Vieja', 'Id_Departamento' => 3],

            // Escuintla
            ['Nom_Municipio' => 'Escuintla', 'Id_Departamento' => 4],
            ['Nom_Municipio' => 'Santa Lucía Cotzumalguapa', 'Id_Departamento' => 4],

            // Petén
            ['Nom_Municipio' => 'Flores', 'Id_Departamento' => 5],
            ['Nom_Municipio' => 'San Benito', 'Id_Departamento' => 5],
        ]);
    }
}