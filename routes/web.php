<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardPedidoController;
use App\Http\Controllers\DashboardDireccionController;
use App\Http\Controllers\DashboardProfileController;
use App\Http\Controllers\DashboardCotizacionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ListaDeseoController;
use App\Http\Controllers\BoletaPagoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\RecurrenteWebhookController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminCategoriaController;
use App\Http\Controllers\Admin\AdminMarcaController;
use App\Http\Controllers\Admin\AdminDepartamentoController;
use App\Http\Controllers\Admin\AdminMunicipioController;
use App\Http\Controllers\Admin\AdminProductoController;
use App\Http\Controllers\Admin\AdminBoletaPagoController;
use App\Http\Controllers\Admin\AdminCotizacionController;
use App\Http\Controllers\Admin\AdminInventarioController;
use App\Http\Controllers\Admin\AdminPedidoController;
use App\Http\Controllers\Admin\AdminUsuarioController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/listadeseo', [ListaDeseoController::class, 'index'])->name('listadeseo.index');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'usuario'])
    ->name('dashboard');

Route::middleware(['auth', 'usuario', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/productos', [AdminProductoController::class, 'index'])->name('productos.index');
        Route::get('/productos/create', [AdminProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [AdminProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/export/{format}', [AdminProductoController::class, 'export'])
            ->whereIn('format', ['xlsx', 'csv'])
            ->name('productos.export');
        Route::get('/productos/{producto}/edit', [AdminProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [AdminProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}', [AdminProductoController::class, 'destroy'])->name('productos.destroy');

        Route::get('/inventario/historial', [AdminInventarioController::class, 'historialIndex'])->name('inventario.historial.index');
        Route::get('/inventario/ventas', [AdminInventarioController::class, 'ventasIndex'])->name('inventario.ventas.index');
        Route::get('/inventario', [AdminInventarioController::class, 'index'])->name('inventario.index');
        Route::get('/inventario/{producto}/ajustar', [AdminInventarioController::class, 'ajustar'])->name('inventario.ajustar');
        Route::post('/inventario/{producto}/ajustar', [AdminInventarioController::class, 'ajustarStore'])->name('inventario.ajustar.store');

        Route::get('/categorias', [AdminCategoriaController::class, 'index'])->name('categorias.index');
        Route::get('/categorias/create', [AdminCategoriaController::class, 'create'])->name('categorias.create');
        Route::post('/categorias', [AdminCategoriaController::class, 'store'])->name('categorias.store');
        Route::get('/categorias/{categoria}/edit', [AdminCategoriaController::class, 'edit'])->name('categorias.edit');
        Route::put('/categorias/{categoria}', [AdminCategoriaController::class, 'update'])->name('categorias.update');
        Route::delete('/categorias/{categoria}', [AdminCategoriaController::class, 'destroy'])->name('categorias.destroy');

        Route::get('/marcas', [AdminMarcaController::class, 'index'])->name('marcas.index');
        Route::get('/marcas/create', [AdminMarcaController::class, 'create'])->name('marcas.create');
        Route::post('/marcas', [AdminMarcaController::class, 'store'])->name('marcas.store');
        Route::get('/marcas/{marca}/edit', [AdminMarcaController::class, 'edit'])->name('marcas.edit');
        Route::put('/marcas/{marca}', [AdminMarcaController::class, 'update'])->name('marcas.update');
        Route::delete('/marcas/{marca}', [AdminMarcaController::class, 'destroy'])->name('marcas.destroy');

        Route::get('/departamentos', [AdminDepartamentoController::class, 'index'])->name('departamentos.index');
        Route::get('/departamentos/create', [AdminDepartamentoController::class, 'create'])->name('departamentos.create');
        Route::post('/departamentos', [AdminDepartamentoController::class, 'store'])->name('departamentos.store');
        Route::get('/departamentos/{departamento}/edit', [AdminDepartamentoController::class, 'edit'])->name('departamentos.edit');
        Route::put('/departamentos/{departamento}', [AdminDepartamentoController::class, 'update'])->name('departamentos.update');
        Route::delete('/departamentos/{departamento}', [AdminDepartamentoController::class, 'destroy'])->name('departamentos.destroy');

        Route::get('/municipios', [AdminMunicipioController::class, 'index'])->name('municipios.index');
        Route::get('/municipios/create', [AdminMunicipioController::class, 'create'])->name('municipios.create');
        Route::post('/municipios', [AdminMunicipioController::class, 'store'])->name('municipios.store');
        Route::get('/municipios/{municipio}/edit', [AdminMunicipioController::class, 'edit'])->name('municipios.edit');
        Route::put('/municipios/{municipio}', [AdminMunicipioController::class, 'update'])->name('municipios.update');
        Route::delete('/municipios/{municipio}', [AdminMunicipioController::class, 'destroy'])->name('municipios.destroy');

        Route::get('/usuarios', [AdminUsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/{usuario}/edit', [AdminUsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [AdminUsuarioController::class, 'update'])->name('usuarios.update');

        Route::get('/boletas', [AdminBoletaPagoController::class, 'index'])->name('boletas.index');
        Route::post('/boletas/{boleta}/aprobar', [AdminBoletaPagoController::class, 'aprobar'])->name('boletas.aprobar');
        Route::post('/boletas/{boleta}/rechazar', [AdminBoletaPagoController::class, 'rechazar'])->name('boletas.rechazar');
        Route::get('/boletas/{boleta}/download', [AdminBoletaPagoController::class, 'download'])->name('boletas.download');
        Route::get('/boletas/{boleta}', [AdminBoletaPagoController::class, 'show'])->name('boletas.show');

        Route::get('/cotizaciones', [AdminCotizacionController::class, 'index'])->name('cotizaciones.index');
        Route::get('/cotizaciones/pendientes', [AdminCotizacionController::class, 'pendientesIndex'])->name('cotizaciones.pendientes.index');
        Route::post('/cotizaciones/{cotizacion}/revision', [AdminCotizacionController::class, 'marcarEnRevision'])->name('cotizaciones.revision');
        Route::get('/cotizaciones/{cotizacion}/emitir', [AdminCotizacionController::class, 'emitir'])->name('cotizaciones.emitir');
        Route::post('/cotizaciones/{cotizacion}/emitir', [AdminCotizacionController::class, 'emitirStore'])->name('cotizaciones.emitir.store');
        Route::get('/cotizaciones/{cotizacion}/download', [AdminCotizacionController::class, 'download'])->name('cotizaciones.download');
        Route::get('/cotizaciones/{cotizacion}', [AdminCotizacionController::class, 'show'])->name('cotizaciones.show');

        Route::get('/pedidos', [AdminPedidoController::class, 'index'])->name('pedidos.index');
        Route::get('/pedidos/historial', [AdminPedidoController::class, 'historialIndex'])->name('pedidos.historial.index');
        Route::get('/pedidos/seguimiento', [AdminPedidoController::class, 'seguimientoIndex'])->name('pedidos.seguimiento.index');
        Route::get('/pedidos/{pedido}/seguimiento', [AdminPedidoController::class, 'seguimiento'])->name('pedidos.seguimiento');
        Route::post('/pedidos/{pedido}/seguimiento/confirmar', [AdminPedidoController::class, 'seguimientoConfirmar'])->name('pedidos.seguimiento.confirmar');
        Route::post('/pedidos/{pedido}/seguimiento/preparacion', [AdminPedidoController::class, 'seguimientoPreparacion'])->name('pedidos.seguimiento.preparacion');
        Route::post('/pedidos/{pedido}/seguimiento/enviado', [AdminPedidoController::class, 'seguimientoEnviado'])->name('pedidos.seguimiento.enviado');
        Route::post('/pedidos/{pedido}/seguimiento/entregado', [AdminPedidoController::class, 'seguimientoEntregado'])->name('pedidos.seguimiento.entregado');
        Route::post('/pedidos/{pedido}/cancelar', [AdminPedidoController::class, 'cancelar'])->name('pedidos.cancelar');
        Route::get('/pedidos/{pedido}/historial', [AdminPedidoController::class, 'historial'])->name('pedidos.historial');
        Route::get('/pedidos/{pedido}', [AdminPedidoController::class, 'show'])->name('pedidos.show');
        Route::delete('/pedidos/{pedido}', [AdminPedidoController::class, 'destroy'])->name('pedidos.destroy');
    });

Route::middleware(['auth', 'usuario'])->group(function () {
    Route::patch('/dashboard/pedidos/{pedido}', [DashboardPedidoController::class, 'update'])
        ->whereNumber('pedido')
        ->name('dashboard.pedidos.update');
    Route::post('/dashboard/pedidos/{pedido}/cancel', [DashboardPedidoController::class, 'cancel'])
        ->whereNumber('pedido')
        ->name('dashboard.pedidos.cancel');
    Route::post('/dashboard/direcciones', [DashboardDireccionController::class, 'store'])
        ->name('dashboard.direcciones.store');
    Route::patch('/dashboard/direcciones/{direccion}', [DashboardDireccionController::class, 'update'])
        ->whereNumber('direccion')
        ->name('dashboard.direcciones.update');
    Route::delete('/dashboard/direcciones/{direccion}', [DashboardDireccionController::class, 'destroy'])
        ->whereNumber('direccion')
        ->name('dashboard.direcciones.destroy');
    Route::patch('/dashboard/profile', [DashboardProfileController::class, 'update'])
        ->name('dashboard.profile.update');
    Route::put('/dashboard/profile/password', [DashboardProfileController::class, 'updatePassword'])
        ->name('dashboard.profile.password');
    Route::post('/dashboard/cotizaciones', [DashboardCotizacionController::class, 'store'])
        ->name('dashboard.cotizaciones.store');
    Route::get('/dashboard/cotizaciones/{cotizacion}/archivo', [DashboardCotizacionController::class, 'download'])
        ->whereNumber('cotizacion')
        ->name('dashboard.cotizaciones.download');
    Route::post('/dashboard/cotizaciones/{cotizacion}/aceptar', [DashboardCotizacionController::class, 'aceptar'])
        ->whereNumber('cotizacion')
        ->name('dashboard.cotizaciones.aceptar');
    Route::post('/dashboard/cotizaciones/{cotizacion}/rechazar', [DashboardCotizacionController::class, 'rechazar'])
        ->whereNumber('cotizacion')
        ->name('dashboard.cotizaciones.rechazar');
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

if (app()->environment('local')) {
    Route::get('/test-whatsapp', [WhatsAppController::class, 'sendNotification'])
        ->name('test-whatsapp');
}
