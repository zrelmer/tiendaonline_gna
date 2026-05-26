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
                'Cate_Imagen' => 'storage/products/p1.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Cate_Nombre' => 'Celulares',
                'Cate_Slug' => 'celulares',
                'Cate_Descripcion' => 'Teléfonos inteligentes',
                'Cate_Imagen' => 'storage/products/p2.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Cate_Nombre' => 'Accesorios',
                'Cate_Slug' => 'accesorios',
                'Cate_Descripcion' => 'Accesorios tecnológicos',
                'Cate_Imagen' => 'storage/products/p3.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
