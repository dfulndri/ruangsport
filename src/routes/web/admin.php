<?php

use App\Http\Controllers\Admin\ClubController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\SportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Verifikasi klub dan venue
        Route::get('/verifikasi', [VerificationController::class, 'index'])->name('verifications.index');
        Route::patch('/verifikasi/{verification}', [VerificationController::class, 'update'])->name('verifications.update');

        // Pengguna
        Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
        Route::patch('/pengguna/{user}', [UserController::class, 'update'])->name('users.update');

        // Moderasi klub dan komunitas
        Route::get('/klub', [ClubController::class, 'index'])->name('clubs.index');
        Route::patch('/klub/{club}/verifikasi', [ClubController::class, 'toggleVerified'])->name('clubs.verify');
        Route::delete('/klub/{club}', [ClubController::class, 'destroy'])->name('clubs.destroy');
        Route::get('/postingan', [PostController::class, 'index'])->name('posts.index');
        Route::delete('/postingan/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

        // Data master
        Route::get('/olahraga', [SportController::class, 'index'])->name('sports.index');
        Route::post('/olahraga', [SportController::class, 'store'])->name('sports.store');
        Route::delete('/olahraga/{sport}', [SportController::class, 'destroy'])->name('sports.destroy');
        Route::get('/lokasi', [LocationController::class, 'index'])->name('locations.index');
        Route::post('/lokasi', [LocationController::class, 'store'])->name('locations.store');
        Route::delete('/lokasi/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
    });
