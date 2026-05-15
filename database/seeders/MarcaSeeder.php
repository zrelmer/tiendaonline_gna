<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tb_marca')->insert([
            ['Nom_Marca' => 'Apple', 'Slug_Marca' => 'apple', 'Descrip_Marca' => 'Marca Apple', 'created_at' => now(), 'updated_at' => now()],
            ['Nom_Marca' => 'Samsung', 'Slug_Marca' => 'samsung', 'Descrip_Marca' => 'Marca Samsung', 'created_at' => now(), 'updated_at' => now()],
            ['Nom_Marca' => 'Dell', 'Slug_Marca' => 'dell', 'Descrip_Marca' => 'Marca Dell', 'created_at' => now(), 'updated_at' => now()],
            ['Nom_Marca' => 'HP', 'Slug_Marca' => 'hp', 'Descrip_Marca' => 'Marca HP', 'created_at' => now(), 'updated_at' => now()],
            ['Nom_Marca' => 'Lenovo', 'Slug_Marca' => 'lenovo', 'Descrip_Marca' => 'Marca Lenovo', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

}