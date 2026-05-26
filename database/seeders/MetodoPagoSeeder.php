<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            ['Id_MetodoPago' => 1, 'MetPag_Descripcion' => 'Tarjeta de Crédito/Débito'],
            // ['Id_MetodoPago' => 2, 'MetPag_Descripcion' => 'PayPal'],
            ['Id_MetodoPago' => 2, 'MetPag_Descripcion' => 'Transferencia Bancaria'],
            ['Id_MetodoPago' => 3, 'MetPag_Descripcion' => 'Efectivo / Contra Entrega'],
            // ['Id_MetodoPago' => 5, 'MetPag_Descripcion' => 'Billetera Digital (Apple Pay/Google Pay)'],
        ];

        foreach ($metodos as $metodo) {
            DB::table('tb_metodopago')->updateOrInsert(
                ['Id_MetodoPago' => $metodo['Id_MetodoPago']],
                ['MetPag_Descripcion' => $metodo['MetPag_Descripcion']]
                // Quitamos el created_at y updated_at ya que en tu modelo pusiste public $timestamps = false;
            );
        }
    }
}
