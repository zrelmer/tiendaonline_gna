<?php

use App\Services\CotizacionService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cotizaciones:marcar-vencidas', function () {
    $actualizadas = app(CotizacionService::class)->sincronizarVencidas();

    $this->info("Cotizaciones marcadas como vencidas: {$actualizadas}");
})->purpose('Marca cotizaciones emitidas cuyo plazo de vigencia expiró');
