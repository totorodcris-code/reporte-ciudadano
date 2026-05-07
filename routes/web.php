<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ================================
// GOOGLE OAUTH RUTAS
// ================================
Route::get('api/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('api/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

// ================================
// RUTAS WEB
// ================================
Route::get('/analytics', [HomeController::class, 'analytics'])->name('analytics');
Route::get('/votos', [HomeController::class, 'votos'])->name('votos');
Route::get('/admin/votos', [HomeController::class, 'adminVotos'])->name('admin.votos');

// ================================
// RUTA PRINCIPAL SPA
// ================================
// Esta ruta catch-all sirve el archivo app.blade.php que contiene la aplicación Vue
// Vue Router se encargará de manejar todas las rutas del cliente
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
