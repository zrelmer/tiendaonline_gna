<?php

namespace App\Providers;

use App\Models\Usuario;
use App\View\Composers\AdminLayoutComposer;
use App\View\Composers\ShopNavComposer;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');

        View::composer('layouts.appadmin', AdminLayoutComposer::class);
        View::composer([
            'layouts.app',
            'welcome',
            'partials.category',
            'partials.home',
            'partials.value',
            'partials.banner1',
            'partials.banner2',
            'partials.header',
            'partials.mobile-menu-offcanvas',
        ], ShopNavComposer::class);

        Authenticate::redirectUsing(
            fn () => route('login')
        );

        RedirectIfAuthenticated::redirectUsing(function ($request) {
            $usuario = $request->user();

            if ($usuario instanceof Usuario && $usuario->esAdministrador()) {
                return route('admin.dashboard');
            }

            return route('dashboard');
        });
    }
}
