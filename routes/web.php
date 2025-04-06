<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NetworkDeviceController;
use App\Http\Controllers\NapController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MonitoringController;

// Redireccionar la ruta principal al login
Route::redirect('/', '/login');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Rutas para gestión de usuarios (solo accesible para administradores)
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::resource('users', UserController::class);
});

// Rutas para gestión de dispositivos de red (OLT y NAP)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('network-devices', NetworkDeviceController::class);
    
    // Rutas para NAPs
    Route::resource('naps', NapController::class);
    Route::get('naps/get-pon-numbers/{networkDevice}', [NapController::class, 'getPonNumbers'])->name('naps.get-pon-numbers');
    
    // Rutas para Clientes
    Route::resource('customers', CustomerController::class);
    Route::get('customers/get-available-ports/{nap}', [CustomerController::class, 'getAvailablePorts'])->name('customers.get-available-ports');
    
    // Rutas para Monitoreo de OLTs
    Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::post('monitoring/run-check', [MonitoringController::class, 'runCheck'])->name('monitoring.run-check');
    Route::post('monitoring/check-device/{id}', [MonitoringController::class, 'checkDevice'])->name('monitoring.check-device');
    Route::post('monitoring/clear-queue', [MonitoringController::class, 'clearQueue'])->name('monitoring.clear-queue');
    Route::post('monitoring/restart-worker', [MonitoringController::class, 'restartWorker'])->name('monitoring.restart-worker');
});

require __DIR__.'/auth.php';
