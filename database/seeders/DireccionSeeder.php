<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DireccionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $direcciones = [];

        // Crear direcciones para usuarios (ID 1 a 41)
        for ($i = 1; $i <= 41; $i++) {
            $direcciones[] = [
                'Id_Usuario' => $i,
                'Direccion' => $faker->streetAddress,
                'Id_Municipio' => $faker->numberBetween(1, 11),
            ];
        }

        DB::table('tb_direccion')->insert($direcciones);
    }
}