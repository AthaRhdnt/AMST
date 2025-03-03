<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\TransaksiController;

Route::middleware(['web'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset.submit');

    Route::middleware(['auth', 'preventBack'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            Route::middleware(['role:Pemilik'])->group(function () {
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

            Route::get('/ubah-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
            Route::post('/ubah-password', [AuthController::class, 'changePassword'])->name('password.update');
        });

        Route::get('/kasir', [TransaksiController::class, 'create'])->name('transaksi.create');
        Route::post('/kasir/store', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::put('/kasir/{transaksi}', [TransaksiController::class, 'statusUpdate'])->name('transaksi.status');
        Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'getDetail'])->name('transaksi.detail');

        Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
        Route::get('/stok/create', [StokController::class, 'create'])->name('stok.create');
        Route::post('stok', [StokController::class, 'store'])->name('stok.store');
        Route::get('/stok/{stok}/edit', [StokController::class, 'edit'])->name('stok.edit');
        Route::put('/stok/{stok}', [StokController::class, 'update'])->name('stok.update');
        Route::delete('/stok/{stok}', [StokController::class, 'destroy'])->name('stok.destroy');
        Route::put('/stok/{stok}/pembelian', [PembelianController::class, 'store'])->name('stok.beli');

        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('menu', [MenuController::class, 'store'])->name('menu.store');
        Route::get('/menu/{menu}/edit', [MenuController::class, 'edit'])->name('menu.edit');
        Route::put('/menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
        Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
        
        Route::get('/riwayat/transaksi', [RiwayatController::class, 'indexTransaksi'])->name('riwayat.index.transaksi');
        Route::get('/riwayat/stok', [RiwayatController::class, 'indexStok'])->name('riwayat.index.stok');

        Route::get('/laporan/transaksi', [LaporanController::class, 'indexTransaksi'])->name('laporan.index.transaksi');
        Route::get('/laporan/transaksi/pdf', [LaporanController::class, 'downloadPdfTransaksi'])->name('laporan.pdf.transaksi');

        Route::get('/laporan/finansial', [LaporanController::class, 'indexFinansial'])->name('laporan.index.finansial');
        Route::get('/laporan/finansial/pdf', [LaporanController::class, 'downloadPdfFinansial'])->name('laporan.pdf.finansial');

        Route::get('/laporan/stok', [LaporanController::class, 'indexStok'])->name('laporan.index.stok');
        Route::get('/laporan/stok/pdf', [LaporanController::class, 'downloadkPdfStok'])->name('laporan.pdf.stok');

        Route::get('/laporan/{transaksi}/{action?}', [LaporanController::class, 'printOrPreview'])->name('struk.action');


        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});