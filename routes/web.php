<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ListaDeseoController;
use App\Http\Controllers\CarritoController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/listadeseo', [ListaDeseoController::class, 'index'])->name('listadeseo.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/producto/{idproducto}/resena', [ProductoController::class, 'saveReview'])
        ->whereNumber('idproducto')
        ->name('product.review.save');
});

Route::get('/{idproducto}/{slug_producto}', [ProductoController::class, 'details'])
    // Cambio: evita conflicto con rutas como /reset-password/{token}.
    ->whereNumber('idproducto')
    ->name('product.details');
require __DIR__.'/auth.php';
Route::get('/cart', [CarritoController::class, 'index'])->name('cart.index');

Route::get('/shop', [ProductoController::class, 'shop'])->name('shop.index');
