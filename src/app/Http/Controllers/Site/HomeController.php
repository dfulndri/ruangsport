<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Club;
use App\Models\Competition;

class HomeController extends Controller
{
    public function index()
    {
        $activities = Activity::with('sport')
            ->where('status', 'open')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(4)
            ->get();

        $clubs = Club::with(['sport', 'location'])
            ->withCount(['memberships as members_count' => fn ($q) => $q->where('status', 'approved')])
            ->orderByDesc('is_verified')
            ->latest()
            ->limit(3)
            ->get();

        $competitions = Competition::with('sport')
            ->withCount(['entries as verified_count' => fn ($q) => $q->where('status', 'verified')])
            ->where('status', '!=', 'draft')
            ->latest()
            ->limit(3)
            ->get();

        return view('home', compact('activities', 'clubs', 'competitions'));
    }
}
