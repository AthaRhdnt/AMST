<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth','removeQueryParams'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/outlet', [OutletController::class, 'index'])->name('outlet.index');
    Route::get('/outlets/create', [OutletController::class, 'create'])->name('outlets.create');
    Route::post('outlets', [OutletController::class, 'store'])->name('outlets.store');
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/reset-dates', [TransaksiController::class, 'resetDateFilters'])->name('transaksi.reset');
    Route::get('/kasir', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/kasir/store', [TransaksiController::class, 'store'])->name('transaksi.store');

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Login routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Logout route
// Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
