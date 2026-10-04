<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organizer\ClubRequest;
use App\Models\Club;
use App\Models\Location;
use App\Models\Sport;
use App\Services\Club\ClubService;
use App\Services\Verification\VerificationService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ClubController extends Controller
{
    public function __construct(private ClubService $clubs)
    {
    }

    public function index()
    {
        $clubs = DashboardController::managedClubs(auth()->user())
            ->with(['sport', 'location'])
            ->withCount([
                'memberships as members_count' => fn ($q) => $q->where('status', 'approved'),
                'memberships as pending_count' => fn ($q) => $q->where('status', 'pending'),
            ])
            ->get();

        return view('organizer.clubs.index', compact('clubs'));
    }

    public function create()
    {
        Gate::authorize('create', Club::class);

        return view('organizer.clubs.create', $this->formData());
    }

    public function store(ClubRequest $request): RedirectResponse
    {
        Gate::authorize('create', Club::class);

        $club = $this->clubs->create($request->user(), $request->validated());

        return redirect()->route('organizer.clubs.index')
            ->with('success', 'Klub "'.$club->name.'" berhasil dibuat. Ajukan verifikasi agar tampil terverifikasi.');
    }

    public function edit(Club $club)
    {
        Gate::authorize('update', $club);

        return view('organizer.clubs.edit', $this->formData() + ['club' => $club]);
    }

    public function update(ClubRequest $request, Club $club): RedirectResponse
    {
        Gate::authorize('update', $club);

        $this->clubs->update($club, $request->validated());

        return redirect()->route('organizer.clubs.index')->with('success', 'Klub berhasil diperbarui.');
    }

    public function destroy(Club $club): RedirectResponse
    {
        Gate::authorize('delete', $club);

        $club->delete();

        return redirect()->route('organizer.clubs.index')->with('success', 'Klub dihapus.');
    }

    public function requestVerification(Club $club, VerificationService $service): RedirectResponse
    {
        Gate::authorize('update', $club);

        try {
            $service->request($club, auth()->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pengajuan verifikasi dikirim. Admin akan meninjaunya.');
    }

    private function formData(): array
    {
        return [
            'sports' => Sport::orderBy('name')->get(),
            'locations' => Location::whereNull('parent_id')->with('children')->orderBy('name')->get(),
        ];
    }
}
