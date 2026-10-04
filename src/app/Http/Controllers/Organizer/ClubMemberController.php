<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\ClubMemberRole;
use App\Enums\ClubMemberStatus;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Services\Club\ClubService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ClubMemberController extends Controller
{
    public function index(Club $club)
    {
        Gate::authorize('manageMembers', $club);

        $memberships = $club->memberships()
            ->with('user')
            ->orderByRaw("case status when 'pending' then 0 when 'approved' then 1 else 2 end")
            ->orderBy('created_at')
            ->get();

        return view('organizer.clubs.members', compact('club', 'memberships'));
    }

    public function update(Request $request, Club $club, int $membership, ClubService $service): RedirectResponse
    {
        Gate::authorize('manageMembers', $club);

        $data = $request->validate([
            'status' => ['nullable', Rule::enum(ClubMemberStatus::class)],
            'role' => ['nullable', Rule::in([ClubMemberRole::Manager->value, ClubMemberRole::Member->value])],
        ]);

        // Hanya owner klub (atau admin) yang boleh mengubah peran jadi manager.
        if (isset($data['role']) && $club->owner_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            return back()->with('error', 'Hanya pemilik klub yang dapat mengubah peran anggota.');
        }

        try {
            $service->updateMember($club->memberships()->findOrFail($membership), array_filter($data));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Anggota diperbarui.');
    }

    public function destroy(Club $club, int $membership): RedirectResponse
    {
        Gate::authorize('manageMembers', $club);

        $member = $club->memberships()->findOrFail($membership);

        if ($member->role === ClubMemberRole::Owner) {
            return back()->with('error', 'Pemilik klub tidak dapat dikeluarkan.');
        }

        $member->delete();

        return back()->with('success', 'Anggota dikeluarkan dari klub.');
    }
}
