<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $usuarios = [];

        // 👤 ADMIN
        $usuarios[] = [
            'Usu_Nombre' => 'Administrador',
            'Usu_Correo' => 'administrador@example.com',
            'Usu_Pass' => Hash::make('password'),
            'Usu_Telefono' => '55555555',
            'Id_Rol' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // 👥 40 usuarios normales
        for ($i = 0; $i < 40; $i++) {
            $usuarios[] = [
                'Usu_Nombre' => $faker->name,
                'Usu_Correo' => $faker->unique()->safeEmail,
                'Usu_Pass' => Hash::make('password'),
                'Usu_Telefono' => $faker->numerify('########'),
                'Id_Rol' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('tb_usuario')->insert($usuarios);
    }
}