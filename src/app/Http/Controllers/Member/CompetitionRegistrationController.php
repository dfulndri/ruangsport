<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Services\Competition\CompetitionRegistrationService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompetitionRegistrationController extends Controller
{
    public function __construct(private CompetitionRegistrationService $service)
    {
    }

    public function store(Request $request, Competition $competition): RedirectResponse
    {
        if (Gate::denies('register', $competition)) {
            return back()->with('error', 'Pendaftaran kompetisi ini sedang tidak dibuka.');
        }

        $data = $request->validate(['team_id' => ['nullable', 'integer', 'exists:teams,id']]);

        try {
            $this->service->register($competition, $request->user(), $data['team_id'] ?? null);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pendaftaran terkirim. Menunggu verifikasi penyelenggara.');
    }

    public function destroy(Request $request, Competition $competition): RedirectResponse
    {
        $data = $request->validate(['team_id' => ['nullable', 'integer', 'exists:teams,id']]);

        try {
            $this->service->withdraw($competition, $request->user(), $data['team_id'] ?? null);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pendaftaran dibatalkan.');
    }
}
