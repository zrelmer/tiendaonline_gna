<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ListaDeseoController;
use App\Http\Controllers\BoletaPagoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\RecurrenteWebhookController;

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

    // API del carrito (JSON) — requiere sesión iniciada
    Route::get('/cart/items', [CarritoController::class, 'items'])->name('cart.items');
    Route::post('/cart/sync', [CarritoController::class, 'sync'])->name('cart.sync');
    Route::post('/cart/items', [CarritoController::class, 'storeItem'])->name('cart.items.store');
    Route::patch('/cart/items/{idProducto}', [CarritoController::class, 'updateItem'])
        ->whereNumber('idProducto')
        ->name('cart.items.update');
    Route::delete('/cart/items/{idProducto}', [CarritoController::class, 'destroyItem'])
        ->whereNumber('idProducto')
        ->name('cart.items.destroy');

    Route::get('/cart/checkout', [CarritoController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/checkout', [CarritoController::class, 'store'])->name('cart.checkout.store');
    Route::get('/cart/checkout/recurrente/success/{pedido}', [CarritoController::class, 'recurrenteSuccess'])
        ->whereNumber('pedido')
        ->name('cart.checkout.recurrente.success');
    Route::get('/cart/checkout/recurrente/cancel/{pedido}', [CarritoController::class, 'recurrenteCancel'])
        ->whereNumber('pedido')
        ->name('cart.checkout.recurrente.cancel');
    Route::post('/cart/checkout/direccion', [CarritoController::class, 'storeDireccion'])->name('cart.checkout.direccion.store');
    Route::post('/boleta-pago', [BoletaPagoController::class, 'store'])->name('boleta-pago.store');

    // API lista de deseos (JSON)
    Route::get('/listadeseo/items', [ListaDeseoController::class, 'items'])->name('listadeseo.items');
    Route::post('/listadeseo/sync', [ListaDeseoController::class, 'sync'])->name('listadeseo.sync');
    Route::post('/listadeseo/items', [ListaDeseoController::class, 'storeItem'])->name('listadeseo.items.store');
    Route::delete('/listadeseo/items/{idProducto}', [ListaDeseoController::class, 'destroyItem'])
        ->whereNumber('idProducto')
        ->name('listadeseo.items.destroy');
});

Route::get('/{idproducto}/{slug_producto}', [ProductoController::class, 'details'])
    // Cambio: evita conflicto con rutas como /reset-password/{token}.
    ->whereNumber('idproducto')
    ->name('product.details');
Route::post('/webhooks/recurrente', RecurrenteWebhookController::class)->name('webhooks.recurrente');
require __DIR__.'/auth.php';
Route::get('/cart', [CarritoController::class, 'index'])->name('cart.index');
Route::get('/shop', [ProductoController::class, 'shop'])->name('shop.index');
