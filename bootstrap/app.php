<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('admin:resumen-stock-semanal')
            ->weeklyOn(1, '8:00')
            ->timezone(config('app.timezone'));
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'webhooks/recurrente',
        ]);

        $middleware->alias([
            'usuario' => \App\Http\Middleware\EnsureAuthenticatedUsuario::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);

        $middleware->priority([
            \Illuminate\Auth\Middleware\Authenticate::class,
            \App\Http\Middleware\EnsureAuthenticatedUsuario::class,
            \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
