<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\DashboardController;

// Home route
Route::get('/', function () {
    return redirect()->route('login');
});
// Route::get('/home2', function () {
//     return view('pages.dashboard.home2');
// })->name('home2');
// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('auth');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/outlet', [OutletController::class, 'index'])->name('outlet.index');
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir');


    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Login routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Logout route
// Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
