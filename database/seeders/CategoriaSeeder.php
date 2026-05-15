<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tb_categoria')->insert([
            [
                'Cate_Nombre' => 'Laptops',
                'Cate_Slug' => 'laptops',
                'Cate_Descripcion' => 'Computadoras portátiles',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Cate_Nombre' => 'Celulares',
                'Cate_Slug' => 'celulares',
                'Cate_Descripcion' => 'Teléfonos inteligentes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Cate_Nombre' => 'Accesorios',
                'Cate_Slug' => 'accesorios',
                'Cate_Descripcion' => 'Accesorios tecnológicos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}