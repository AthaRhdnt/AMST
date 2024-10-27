<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Home route - requires authentication
Route::get('/', function () {
    return view('pages.dashboard.home');
})->name('home')->middleware('auth');
// Route::get('/home2', function () {
//     return view('pages.dashboard.home2');
// })->name('home2');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');


// Login routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Logout route
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
