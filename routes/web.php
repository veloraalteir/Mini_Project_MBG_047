<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DistributionLookupController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
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

    // Bukti pendukung (privat, dicek di controller)
    Route::get('/reports/{report}/evidence', [EvidenceController::class, 'show'])->name('evidence.show');

    // Fitur pelapor
    Route::middleware('role:pelapor')->group(function () {
        Route::resource('reports', ReportController::class)->except(['destroy']);
    });

    // Fitur pemantau & koordinator
    Route::middleware('role:pemantau,koordinator')->prefix('review')->name('review.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::get('/{report}', [ReviewController::class, 'show'])->name('show');
        Route::post('/{report}/transition', [ReviewController::class, 'transition'])->name('transition');
    });
});