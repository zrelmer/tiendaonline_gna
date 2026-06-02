<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            UsuarioSeeder::class,
            EstatusSeeder::class,
            CategoriaSeeder::class,
            MarcaSeeder::class,
            ProductoSeeder::class,
            DepartamentoSeeder::class,
            MunicipioSeeder::class,
            DireccionSeeder::class,
            MovimientoSeeder::class,
            InventarioSeeder::class,
            MetodoPagoSeeder::class,
            CarritoSeeder::class,
            CarritoDetalleSeeder::class,
            PedidoSeeder::class,
            DetallePedidoSeeder::class,
            PedidoHistorialSeeder::class,
            PagoSeeder::class,
            EnvioSeeder::class,
            ProdImagenSeeder::class,
            ComentarioSeeder::class,
        ]);
    }
}
