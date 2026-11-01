<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DistributionLookupController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', fn () => view('home'))->name('home');

    // Endpoint JSON untuk fetch(): distribusi berdasarkan sekolah
    Route::get('/schools/{school}/distributions', [DistributionLookupController::class, 'bySchool'])
        ->name('schools.distributions');
});