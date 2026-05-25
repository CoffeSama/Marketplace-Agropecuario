<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SolicitudVendedorController;
use App\Http\Controllers\ProductoController;

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
});

// PENDIENTE DE VERIFICACIÓN - subir documentos
Route::middleware(['auth', 'role.cliente'])->group(function () {
    Route::get('/verificacion', [SolicitudVendedorController::class, 'create'])->name('verificacion.create');
    Route::post('/verificacion', [SolicitudVendedorController::class, 'store'])->name('verificacion.store');
});
