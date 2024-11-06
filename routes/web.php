<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset.submit');

Route::middleware(['auth',
// 'removeQueryParams'
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/outlets', [OutletController::class, 'index'])->name('outlets.index');
    Route::get('/outlets/create', [OutletController::class, 'create'])->name('outlets.create');
    Route::post('outlets', [OutletController::class, 'store'])->name('outlets.store');
    Route::get('/outlets/{outlets}/edit', [OutletController::class, 'edit'])->name('outlets.edit');
    Route::put('/outlets/{outlets}', [OutletController::class, 'update'])->name('outlets.update');
    Route::delete('/outlets/{outlets}', [OutletController::class, 'destroy'])->name('outlets.destroy');
    Route::put('/outlets/{outlets}/reset', [OutletController::class, 'reset'])->name('outlets.reset');

    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
    Route::get('/stok/create', [StokController::class, 'create'])->name('stok.create');
    Route::post('stok', [StokController::class, 'store'])->name('stok.store');
    Route::get('/stok/{stok}/edit', [StokController::class, 'edit'])->name('stok.edit');
    Route::put('/stok/{stok}', [StokController::class, 'update'])->name('stok.update');
    Route::delete('/stok/{stok}', [StokController::class, 'destroy'])->name('stok.destroy');

    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
    Route::post('menu', [MenuController::class, 'store'])->name('menu.store');
    Route::get('/menu/{menu}/edit', [MenuController::class, 'edit'])->name('menu.edit');
    Route::put('/menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/create', [LaporanController::class, 'create'])->name('laporan.create');
    Route::post('laporan', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{laporan}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{laporan}', [LaporanController::class, 'update'])->name('laporan.update');
    Route::delete('/laporan/{laporan}', [LaporanController::class, 'destroy'])->name('laporan.destroy');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/create', [RiwayatController::class, 'create'])->name('riwayat.create');
    Route::post('riwayat', [RiwayatController::class, 'store'])->name('riwayat.store');
    Route::get('/riwayat/{riwayat}/edit', [RiwayatController::class, 'edit'])->name('riwayat.edit');
    Route::put('/riwayat/{riwayat}', [RiwayatController::class, 'update'])->name('riwayat.update');
    Route::delete('/riwayat/{riwayat}', [RiwayatController::class, 'destroy'])->name('riwayat.destroy');

    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/reset-dates', [TransaksiController::class, 'resetDateFilters'])->name('transaksi.reset');
    Route::get('/kasir', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/kasir/store', [TransaksiController::class, 'store'])->name('transaksi.store');

    Route::middleware(['role:Pemilik'])->group(function () {
        Route::get('/ubah-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
        Route::post('/ubah-password', [AuthController::class, 'changePassword'])->name('password.update');
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});