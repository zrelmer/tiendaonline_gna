<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tb_marca')->insert([
            ['Nom_Marca' => 'Apple', 'slug_Marca' => 'apple', 'Descrip_Marca' => 'Marca Apple', 'created_at' => now(), 'updated_at' => now()],
            ['Nom_Marca' => 'Samsung', 'slug_Marca' => 'samsung', 'Descrip_Marca' => 'Marca Samsung', 'created_at' => now(), 'updated_at' => now()],
            ['Nom_Marca' => 'Dell', 'slug_Marca' => 'dell', 'Descrip_Marca' => 'Marca Dell', 'created_at' => now(), 'updated_at' => now()],
            ['Nom_Marca' => 'HP', 'slug_Marca' => 'hp', 'Descrip_Marca' => 'Marca HP', 'created_at' => now(), 'updated_at' => now()],
            ['Nom_Marca' => 'Lenovo', 'slug_Marca' => 'lenovo', 'Descrip_Marca' => 'Marca Lenovo', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

}