<?php

namespace Database\Seeders;

use App\Models\Movimiento;
use Illuminate\Database\Seeder;

class MovimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'Salida por pedido',
            'Devolución por cancelación',
            'Ajuste manual',
            'Reserva por pedido',
            'Liberación de reserva',
        ] as $nombre) {
            Movimiento::query()->firstOrCreate([
                'Nom_Movimiento' => $nombre,
            ]);
        }
    }
}
