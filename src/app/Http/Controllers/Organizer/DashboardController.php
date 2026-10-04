<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Club;
use App\Models\Competition;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $clubs = $this->managedClubs($user)->with('sport')->withCount([
            'memberships as members_count' => fn ($q) => $q->where('status', 'approved'),
            'memberships as pending_count' => fn ($q) => $q->where('status', 'pending'),
        ])->get();

        $clubIds = $clubs->pluck('id');

        $activities = Activity::with('sport')
            ->where(fn ($q) => $q->where('organizer_id', $user->id)->orWhereIn('club_id', $clubIds))
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(5)
            ->get();

        $competitions = Competition::with('sport')
            ->where(fn ($q) => $q->where('organizer_id', $user->id)->orWhereIn('club_id', $clubIds))
            ->latest()
            ->limit(5)
            ->get();

        return view('organizer.dashboard', compact('clubs', 'activities', 'competitions'));
    }

    /** Klub yang dikelola user (admin melihat semua klub). */
    public static function managedClubs($user)
    {
        return Club::query()
            ->when(! $user->isAdmin(), fn ($q) => $q->whereIn('id', $user->clubMemberships()
                ->where('status', 'approved')
                ->whereIn('role', ['owner', 'manager'])
                ->pluck('club_id')))
            ->orderBy('name');
    }
}
