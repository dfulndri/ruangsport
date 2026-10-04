<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityParticipant;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Competition;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'clubs' => Club::count(),
            'verified_clubs' => Club::where('is_verified', true)->count(),
            'organizers' => ClubMember::where('status', 'approved')->whereIn('role', ['owner', 'manager'])->distinct()->count('user_id'),
            'activities' => Activity::count(),
            'competitions' => Competition::count(),
            'participants' => ActivityParticipant::whereIn('status', ['registered', 'attended', 'no_show'])->count(),
            'pending_verifications' => Verification::where('status', 'pending')->count(),
        ];

        $topSports = Activity::query()
            ->select('sport_id', DB::raw('count(*) as total'))
            ->groupBy('sport_id')
            ->orderByDesc('total')
            ->with('sport')
            ->limit(5)
            ->get();

        $months = collect(range(5, 0))->map(function (int $ago) {
            $start = now()->startOfMonth()->subMonths($ago);

            return [
                'label' => $start->translatedFormat('F Y'),
                'total' => Activity::whereBetween('starts_at', [$start, $start->copy()->endOfMonth()])->count(),
            ];
        });

        return view('admin.dashboard', compact('stats', 'topSports', 'months'));
    }
}
