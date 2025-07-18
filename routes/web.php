<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\Admin\DataController;
use App\Http\Controllers\ProfileController;

// ✅ Halaman utama (public)
Route::redirect('/', '/monitoring');

// ✅ Dashboard user setelah login
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ✅ Monitoring (akses umum)
Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');

// ✅ Fitur input dan edit data Monitoring (khusus user login)
Route::middleware('auth')->group(function () {
    // MonitoringController
    Route::get('/monitoring/create', [MonitoringController::class, 'create'])->name('monitoring.create');
    Route::post('/monitoring', [MonitoringController::class, 'store'])->name('monitoring.store');
    Route::get('/monitoring/{id}/edit', [MonitoringController::class, 'edit'])->name('monitoring.edit');
    Route::put('/monitoring/{id}', [MonitoringController::class, 'update'])->name('monitoring.update');

    // DataController - untuk input dan edit model log & distribusi
    Route::get('/data', [DataController::class, 'index'])->name('data.index');
    Route::get('/data/create/{model}', [DataController::class, 'create'])->name('data.create');
    Route::post('/data/store/{model}', [DataController::class, 'store'])->name('data.store');
    Route::get('/data/{model}/{id}/edit', [DataController::class, 'edit'])->name('data.edit');
    Route::put('/data/{model}/{id}', [DataController::class, 'update'])->name('data.update');

    // Profile setting dari Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Route auth login/register/logout dari Breeze
require __DIR__.'/auth.php';
