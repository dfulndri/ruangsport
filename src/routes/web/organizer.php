<?php

use App\Http\Controllers\Organizer\ActivityController;
use App\Http\Controllers\Organizer\ActivityParticipantController;
use App\Http\Controllers\Organizer\BracketController;
use App\Http\Controllers\Organizer\ClubController;
use App\Http\Controllers\Organizer\ClubMemberController;
use App\Http\Controllers\Organizer\CompetitionController;
use App\Http\Controllers\Organizer\CompetitionEntryController;
use App\Http\Controllers\Organizer\DashboardController;
use App\Http\Controllers\Organizer\MatchController;
use Illuminate\Support\Facades\Route;

// Hak akses tiap aksi dicek di controller lewat Policy (Gate::authorize).
Route::prefix('organizer')->name('organizer.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Klub
    Route::get('/klub', [ClubController::class, 'index'])->name('clubs.index');
    Route::get('/klub/buat', [ClubController::class, 'create'])->name('clubs.create');
    Route::post('/klub', [ClubController::class, 'store'])->name('clubs.store');
    Route::get('/klub/{club:slug}/ubah', [ClubController::class, 'edit'])->name('clubs.edit');
    Route::put('/klub/{club:slug}', [ClubController::class, 'update'])->name('clubs.update');
    Route::delete('/klub/{club:slug}', [ClubController::class, 'destroy'])->name('clubs.destroy');
    Route::post('/klub/{club:slug}/verifikasi', [ClubController::class, 'requestVerification'])->name('clubs.verification');

    // Anggota klub
    Route::get('/klub/{club:slug}/anggota', [ClubMemberController::class, 'index'])->name('clubs.members');
    Route::patch('/klub/{club:slug}/anggota/{membership}', [ClubMemberController::class, 'update'])->name('clubs.members.update');
    Route::delete('/klub/{club:slug}/anggota/{membership}', [ClubMemberController::class, 'destroy'])->name('clubs.members.destroy');

    // Aktivitas
    Route::get('/aktivitas', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/aktivitas/buat', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/aktivitas', [ActivityController::class, 'store'])->name('activities.store');
    Route::get('/aktivitas/{activity:slug}/ubah', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('/aktivitas/{activity:slug}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/aktivitas/{activity:slug}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    // Peserta aktivitas & kehadiran
    Route::get('/aktivitas/{activity:slug}/peserta', [ActivityParticipantController::class, 'index'])->name('activities.participants');
    Route::patch('/aktivitas/{activity:slug}/peserta/{participant}', [ActivityParticipantController::class, 'update'])->name('activities.participants.update');

    // Kompetisi
    Route::get('/kompetisi', [CompetitionController::class, 'index'])->name('competitions.index');
    Route::get('/kompetisi/buat', [CompetitionController::class, 'create'])->name('competitions.create');
    Route::post('/kompetisi', [CompetitionController::class, 'store'])->name('competitions.store');
    Route::get('/kompetisi/{competition:slug}/ubah', [CompetitionController::class, 'edit'])->name('competitions.edit');
    Route::put('/kompetisi/{competition:slug}', [CompetitionController::class, 'update'])->name('competitions.update');
    Route::delete('/kompetisi/{competition:slug}', [CompetitionController::class, 'destroy'])->name('competitions.destroy');
    Route::get('/kompetisi/{competition:slug}/kelola', [CompetitionController::class, 'manage'])->name('competitions.manage');

    // Peserta, bracket, dan pertandingan kompetisi
    Route::patch('/kompetisi/{competition:slug}/peserta/{entry}', [CompetitionEntryController::class, 'update'])->name('competitions.entries.update');
    Route::post('/kompetisi/{competition:slug}/bracket', [BracketController::class, 'store'])->name('competitions.bracket');
    Route::patch('/kompetisi/{competition:slug}/pertandingan/{match}', [MatchController::class, 'update'])->name('competitions.matches.update');
});
