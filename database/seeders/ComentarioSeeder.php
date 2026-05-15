<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\Comentario;

class ComentarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = Usuario::all();
        $productos = Producto::all();

        if ($usuarios->isEmpty() || $productos->isEmpty()) {
            echo "⚠️ Debes ejecutar primero seeders de usuarios y productos\n";
            return;
        }

        $comentarios = [
            "Excelente producto, superó mis expectativas.",
            "Muy buena calidad, lo recomiendo totalmente.",
            "Cumple con lo prometido, volvería a comprar.",
            "Buena relación calidad-precio.",
            "El envío fue rápido y el producto llegó en buen estado.",
            "No me gustó tanto como esperaba.",
            "Está bien, pero podría mejorar.",
            "Muy satisfecho con la compra.",
            "Producto de buena calidad, recomendado.",
            "No cumplió con mis expectativas.",
            "Funciona perfectamente, excelente compra.",
            "Regular, hay mejores opciones.",
            "Me encantó, lo compraría nuevamente.",
        ];

        foreach ($productos as $producto) {

            // Cada producto tendrá entre 2 y 5 comentarios
            for ($i = 0; $i < rand(2, 5); $i++) {

                Comentario::create([
                    'Id_Usuario' => $usuarios->random()->Id_Usuario,
                    'Id_Producto' => $producto->Id_Producto,
                    'Rating' => rand(3, 5), // 🔥 puedes cambiar a 1-5 si quieres más variedad
                    'Comentario' => $comentarios[array_rand($comentarios)],
                ]);
            }
        }

        echo "✅ Comentarios insertados correctamente\n";
    }
}
