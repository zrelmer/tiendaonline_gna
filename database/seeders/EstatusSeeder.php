<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstatusSeeder extends Seeder
{
    public function run(): void
    {
        $estatus = [
            // Catálogo / producto
            ['Id_Estatus' => 1, 'Nom_Estatus' => 'Activo'],
            ['Id_Estatus' => 2, 'Nom_Estatus' => 'Inactivo'],
            ['Id_Estatus' => 3, 'Nom_Estatus' => 'Agotado'],
            ['Id_Estatus' => 4, 'Nom_Estatus' => 'Pendiente'],

            // Pago
            ['Id_Estatus' => 10, 'Nom_Estatus' => 'Pago pendiente comprobante'],
            ['Id_Estatus' => 11, 'Nom_Estatus' => 'Pago pendiente verificación'],
            ['Id_Estatus' => 12, 'Nom_Estatus' => 'Pago pendiente cobro'],
            ['Id_Estatus' => 13, 'Nom_Estatus' => 'Pagado'],
            ['Id_Estatus' => 14, 'Nom_Estatus' => 'Pago rechazado'],

            // Pedido
            ['Id_Estatus' => 20, 'Nom_Estatus' => 'Pedido pendiente'],
            ['Id_Estatus' => 21, 'Nom_Estatus' => 'Pedido confirmado'],
            ['Id_Estatus' => 22, 'Nom_Estatus' => 'En preparación'],
            ['Id_Estatus' => 23, 'Nom_Estatus' => 'Enviado'],
            ['Id_Estatus' => 24, 'Nom_Estatus' => 'Entregado'],
            ['Id_Estatus' => 25, 'Nom_Estatus' => 'Cancelado'],

            // Envío
            ['Id_Estatus' => 30, 'Nom_Estatus' => 'Envío pendiente'],
            ['Id_Estatus' => 31, 'Nom_Estatus' => 'En tránsito'],
            ['Id_Estatus' => 32, 'Nom_Estatus' => 'Envío entregado'],
        ];

        foreach ($estatus as $fila) {
            DB::table('tb_estatus')->updateOrInsert(
                ['Id_Estatus' => $fila['Id_Estatus']],
                ['Nom_Estatus' => $fila['Nom_Estatus']]
            );
        }
    }
}
