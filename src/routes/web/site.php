<?php

use App\Http\Controllers\Site\ActivityController;
use App\Http\Controllers\Site\ClubController;
use App\Http\Controllers\Site\CommunityController;
use App\Http\Controllers\Site\CompetitionController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\VenueController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/klub', [ClubController::class, 'index'])->name('clubs.index');
Route::get('/klub/{club:slug}', [ClubController::class, 'show'])->name('clubs.show');

Route::get('/aktivitas', [ActivityController::class, 'index'])->name('activities.index');
Route::get('/aktivitas/{activity:slug}', [ActivityController::class, 'show'])->name('activities.show');

Route::get('/kompetisi', [CompetitionController::class, 'index'])->name('competitions.index');
Route::get('/kompetisi/{competition:slug}', [CompetitionController::class, 'show'])->name('competitions.show');

Route::get('/venue', [VenueController::class, 'index'])->name('venues.index');
Route::get('/venue/{venue:slug}', [VenueController::class, 'show'])->name('venues.show');

Route::get('/komunitas', [CommunityController::class, 'index'])->name('community.index');
