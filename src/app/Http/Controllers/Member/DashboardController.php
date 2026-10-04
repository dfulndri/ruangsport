<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $clubMemberships = $user->clubMemberships()
            ->whereHas('club')
            ->with('club.sport')
            ->get();

        $upcoming = $user->activityParticipations()
            ->whereIn('status', ['registered', 'waitlisted'])
            ->whereHas('activity', fn ($q) => $q->where('starts_at', '>=', now()))
            ->with('activity')
            ->get()
            ->sortBy(fn ($p) => $p->activity->starts_at)
            ->values();

        $history = $user->activityParticipations()
            ->whereHas('activity')
            ->where(fn ($q) => $q->whereIn('status', ['attended', 'no_show', 'cancelled'])
                ->orWhereHas('activity', fn ($a) => $a->where('starts_at', '<', now())))
            ->with('activity')
            ->latest('registered_at')
            ->limit(20)
            ->get();

        return view('member.dashboard', compact('user', 'clubMemberships', 'upcoming', 'history'));
    }
}
