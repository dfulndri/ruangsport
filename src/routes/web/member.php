<?php

use App\Http\Controllers\Member\ActivityRegistrationController;
use App\Http\Controllers\Member\ClubMembershipController;
use App\Http\Controllers\Member\CommentController;
use App\Http\Controllers\Member\CompetitionRegistrationController;
use App\Http\Controllers\Member\DashboardController;
use App\Http\Controllers\Member\PostController;
use App\Http\Controllers\Member\PostModerationController;
use App\Http\Controllers\Member\TeamController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Klub
    Route::post('/klub/{club:slug}/gabung', [ClubMembershipController::class, 'store'])->name('clubs.join');
    Route::delete('/klub/{club:slug}/keluar', [ClubMembershipController::class, 'destroy'])->name('clubs.leave');

    // RSVP aktivitas
    Route::post('/aktivitas/{activity:slug}/rsvp', [ActivityRegistrationController::class, 'store'])->name('activities.rsvp');
    Route::delete('/aktivitas/{activity:slug}/rsvp', [ActivityRegistrationController::class, 'destroy'])->name('activities.cancel');

    // Pendaftaran kompetisi (individu atau tim)
    Route::post('/kompetisi/{competition:slug}/daftar', [CompetitionRegistrationController::class, 'store'])->name('competitions.register');
    Route::delete('/kompetisi/{competition:slug}/daftar', [CompetitionRegistrationController::class, 'destroy'])->name('competitions.withdraw');

    // Tim
    Route::get('/tim', [TeamController::class, 'index'])->name('teams.index');
    Route::post('/tim', [TeamController::class, 'store'])->name('teams.store');
    Route::delete('/tim/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
    Route::post('/tim/{team}/anggota', [TeamController::class, 'addMember'])->name('teams.members.store');
    Route::delete('/tim/{team}/anggota/{user}', [TeamController::class, 'removeMember'])->name('teams.members.destroy');

    // Komunitas
    Route::post('/komunitas', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/komunitas/{post}', [PostModerationController::class, 'destroyPost'])->name('posts.destroy');
    Route::post('/komunitas/{post}/komentar', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/komentar/{comment}', [PostModerationController::class, 'destroyComment'])->name('comments.destroy');
});
