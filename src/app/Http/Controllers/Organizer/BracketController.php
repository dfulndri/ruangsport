<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Services\Competition\BracketGenerator;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BracketController extends Controller
{
    /** Buat (atau buat ulang) bracket sistem gugur dari peserta terverifikasi. */
    public function store(Request $request, Competition $competition, BracketGenerator $generator): RedirectResponse
    {
        Gate::authorize('manageMatches', $competition);

        try {
            $generator->generate($competition, $request->boolean('reshuffle'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Bracket berhasil dibuat. Kompetisi kini berstatus berlangsung.');
    }
}
