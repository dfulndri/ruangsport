<?php

use App\Http\Controllers\VenueOwner\VenueController;
use Illuminate\Support\Facades\Route;

Route::prefix('pengelola-venue')
    ->name('venue-owner.')
    ->middleware(['auth', 'role:venue_owner,admin'])
    ->group(function () {
        Route::get('/', [VenueController::class, 'index'])->name('venues.index');
        Route::get('/buat', [VenueController::class, 'create'])->name('venues.create');
        Route::post('/', [VenueController::class, 'store'])->name('venues.store');
        Route::get('/{venue:slug}/ubah', [VenueController::class, 'edit'])->name('venues.edit');
        Route::put('/{venue:slug}', [VenueController::class, 'update'])->name('venues.update');
        Route::delete('/{venue:slug}', [VenueController::class, 'destroy'])->name('venues.destroy');
        Route::post('/{venue:slug}/verifikasi', [VenueController::class, 'requestVerification'])->name('venues.verification');
    });
