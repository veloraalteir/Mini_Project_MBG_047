<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DistributionLookupController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\MasterDistributionController;
use App\Http\Controllers\MasterSchoolController;
use App\Http\Controllers\RekapController;
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
    Route::middleware('role:pemantau,koordinator')->group(function () {
        Route::prefix('review')->name('review.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::get('/{report}', [ReviewController::class, 'show'])->name('show');
            Route::post('/{report}/transition', [ReviewController::class, 'transition'])->name('transition');
        });

        Route::get('/rekap', [RekapController::class, 'index'])->name('rekap');
    });

    // Fitur koordinator: data pendukung
    Route::middleware('role:koordinator')->prefix('master')->name('master.')->group(function () {
        Route::get('/schools', [MasterSchoolController::class, 'index'])->name('schools.index');
        Route::post('/schools', [MasterSchoolController::class, 'store'])->name('schools.store');
        Route::delete('/schools/{school}', [MasterSchoolController::class, 'destroy'])->name('schools.destroy');

        Route::get('/distributions', [MasterDistributionController::class, 'index'])->name('distributions.index');
        Route::post('/distributions', [MasterDistributionController::class, 'store'])->name('distributions.store');
        Route::delete('/distributions/{distribution}', [MasterDistributionController::class, 'destroy'])->name('distributions.destroy');
    });
});