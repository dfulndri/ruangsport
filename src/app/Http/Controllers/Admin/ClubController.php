<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    public function index(Request $request)
    {
        $clubs = Club::query()
            ->with(['sport', 'owner', 'location'])
            ->withCount(['memberships as members_count' => fn ($q) => $q->where('status', 'approved')])
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.clubs.index', compact('clubs'));
    }

    /** Ubah status terverifikasi langsung oleh admin. */
    public function toggleVerified(Club $club): RedirectResponse
    {
        $club->update(['is_verified' => ! $club->is_verified]);

        return back()->with('success', $club->is_verified ? 'Klub ditandai terverifikasi.' : 'Verifikasi klub dicabut.');
    }

    public function destroy(Club $club): RedirectResponse
    {
        $club->delete();

        return back()->with('success', 'Klub dihapus.');
    }
}
