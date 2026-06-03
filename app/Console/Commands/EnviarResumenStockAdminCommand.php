<?php

namespace App\Console\Commands;

use App\Services\AdminInventarioService;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;

class EnviarResumenStockAdminCommand extends Command
{
    protected $signature = 'admin:resumen-stock-semanal';

    protected $description = 'Envía el resumen semanal de stock bajo y sin stock al equipo administrativo';

    public function handle(
        AdminInventarioService $inventarioService,
        AdminNotificationService $adminNotificationService
    ): int {
        $umbral = $inventarioService->umbralBajoStock();
        $bajoStock = $inventarioService->productosBajoStockParaResumen();
        $sinStock = $inventarioService->productosSinStockParaResumen();

        $adminNotificationService->resumenStockSemanal($bajoStock, $sinStock, $umbral);

        $this->info(sprintf(
            'Resumen enviado: %d bajo stock, %d sin stock.',
            $bajoStock->count(),
            $sinStock->count()
        ));

        return self::SUCCESS;
    }
}
