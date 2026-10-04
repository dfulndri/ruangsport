<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Sport;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    public function index(Request $request)
    {
        $clubs = Club::query()
            ->with(['sport', 'location'])
            ->withCount(['memberships as members_count' => fn ($q) => $q->where('status', 'approved')])
            ->when($request->filled('sport'), fn ($q) => $q->where('sport_id', $request->integer('sport')))
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->orderByDesc('is_verified')
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        $sports = Sport::orderBy('name')->get();

        return view('clubs.index', compact('clubs', 'sports'));
    }

    public function show(Club $club)
    {
        $club->load(['sport', 'location', 'owner'])
            ->loadCount(['memberships as members_count' => fn ($q) => $q->where('status', 'approved')]);

        $membership = auth()->user()?->clubMemberships()->where('club_id', $club->id)->first();

        $activities = $club->activities()
            ->with('sport')
            ->where('status', '!=', 'draft')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(6)
            ->get();

        $members = $club->memberships()
            ->with('user')
            ->where('status', 'approved')
            ->orderByRaw("case role when 'owner' then 0 when 'manager' then 1 else 2 end")
            ->limit(40)
            ->get();

        return view('clubs.show', compact('club', 'membership', 'activities', 'members'));
    }
}
