<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SolicitudVendedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PreventaController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PedidoController;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/registro', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/registro', [RegisterController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->post('/logout', [LoginController::class, 'logout'])->name('logout');

// ADMINISTRADOR
Route::middleware(['auth', 'role.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Validación de documentos / solicitudes de usuarios
    Route::get('/solicitudes', [SolicitudVendedorController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/{solicitudVendedor}', [SolicitudVendedorController::class, 'show'])->name('solicitudes.show');
    Route::post('/solicitudes/{id}/aprobar', [SolicitudVendedorController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{id}/rechazar', [SolicitudVendedorController::class, 'rechazar'])->name('solicitudes.rechazar');
});

// USUARIOS AUTENTICADOS
Route::middleware('auth')->group(function () {
    Route::get('/inicio', fn() => view('home'))->name('home');

    // Perfil y ubicación GPS del productor
    Route::get('/perfil/ubicacion', [SolicitudVendedorController::class, 'ubicacion'])->name('perfil.ubicacion');
    Route::post('/perfil/ubicacion', [SolicitudVendedorController::class, 'guardarUbicacion'])->name('perfil.ubicacion.guardar');

    // US05 - Publicación de cosecha
    Route::get('/marketplace', [ProductoController::class, 'marketplace'])->name('productos.marketplace');
    Route::get('/mis-productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/mis-productos/agregar', [ProductoController::class, 'create'])->name('productos.create');
    Route::post('/mis-productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::get('/mis-productos/{producto}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/mis-productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    
    // US06 - Preventa de cosecha
    Route::post('/preventas/{producto}', [PreventaController::class, 'store'])->name('preventas.store');
    Route::get('/mis-preventas', [PreventaController::class, 'misPreventas'])->name('mis-preventas');
    Route::get('/ventas-futuras', [PreventaController::class, 'ventasFuturas'])->name('ventas-futuras');
    Route::get('/preventas/{preventa}/pagar-anticipo', [PreventaController::class, 'mostrarPagoAnticipo'])->name('preventas.pagar-anticipo');
    Route::post('/preventas/{preventa}/pagar-anticipo', [PreventaController::class, 'confirmarPagoAnticipo'])->name('preventas.pagar-anticipo.confirmar');
    Route::get('/preventas/{preventa}/pagar-saldo', [PreventaController::class, 'mostrarPagoSaldo'])->name('preventas.pagar-saldo');
    Route::post('/preventas/{preventa}/pagar-saldo', [PreventaController::class, 'confirmarPagoSaldo'])->name('preventas.pagar-saldo.confirmar');

    // US10 - Carrito de Compras
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/agregar/{producto}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::patch('/carrito/actualizar/{producto}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
    Route::delete('/carrito/eliminar/{producto}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
    Route::delete('/carrito/vaciar', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');

    // US11 - Confirmación de pedido y validación del productor
    Route::post('/pedidos/confirmar', [PedidoController::class, 'confirmar'])->name('pedidos.confirmar');
    Route::get('/mis-pedidos', [PedidoController::class, 'misPedidos'])->name('pedidos.mis-pedidos');
    Route::get('/pedidos-recibidos', [PedidoController::class, 'recibidos'])->name('pedidos.recibidos');
    Route::post('/pedidos/{pedido}/aceptar', [PedidoController::class, 'aceptar'])->name('pedidos.aceptar');
    Route::post('/pedidos/{pedido}/rechazar', [PedidoController::class, 'rechazar'])->name('pedidos.rechazar');
    Route::get('/pedidos/{pedido}/pagar', [PedidoController::class, 'mostrarPagoQr'])->name('pedidos.pagar');
    Route::post('/pedidos/{pedido}/pagar', [PedidoController::class, 'confirmarPagoQr'])->name('pedidos.pagar.confirmar');
});

// PENDIENTE DE VERIFICACIÓN - subir documentos
Route::middleware(['auth', 'role.cliente'])->group(function () {
    Route::get('/verificacion', [SolicitudVendedorController::class, 'create'])->name('verificacion.create');
    Route::post('/verificacion', [SolicitudVendedorController::class, 'store'])->name('verificacion.store');
});
