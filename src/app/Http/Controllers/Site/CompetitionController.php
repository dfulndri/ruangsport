<?php

namespace App\Http\Controllers\Site;

use App\Enums\CompetitionStatus;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionEntry;
use App\Models\Team;
use App\Support\Bracket;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function index(Request $request)
    {
        $competitions = Competition::query()
            ->with(['sport', 'organizer'])
            ->withCount(['entries as verified_count' => fn ($q) => $q->where('status', 'verified')])
            ->where('status', '!=', CompetitionStatus::Draft->value)
            ->when($request->filled('sport'), fn ($q) => $q->where('sport_id', $request->integer('sport')))
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->orderByDesc('starts_at')
            ->paginate(9)
            ->withQueryString();

        return view('competitions.index', compact('competitions'));
    }

    public function show(Competition $competition)
    {
        $user = auth()->user();
        $canManage = $user && $user->can('manageEntries', $competition);

        // Draft hanya terlihat oleh pengelolanya.
        abort_if($competition->status === CompetitionStatus::Draft && ! $canManage, 404);

        $competition->load(['sport', 'organizer', 'club', 'venue']);

        $entries = $competition->verifiedEntries()->with(['user', 'team'])->get();

        $matches = $competition->matches()
            ->with(['entryA.user', 'entryA.team', 'entryB.user', 'entryB.team'])
            ->get();

        $rounds = $matches->groupBy('round');
        $lastRound = (int) $rounds->keys()->max();

        $roundNames = $rounds->keys()->mapWithKeys(fn ($round) => [$round => Bracket::roundName($round, $lastRound)]);

        // Data kotak pendaftaran untuk user yang login.
        $myTeams = collect();
        $myEntries = collect();
        $canRegister = false;

        if ($user) {
            $canRegister = $user->can('register', $competition);

            if ($competition->isTeam()) {
                $myTeams = Team::where('sport_id', $competition->sport_id)
                    ->where(fn ($q) => $q->where('created_by', $user->id)
                        ->orWhereHas('members', fn ($m) => $m->where('users.id', $user->id)->where('team_members.role', 'captain')))
                    ->orderBy('name')
                    ->get();

                $myEntries = CompetitionEntry::where('competition_id', $competition->id)
                    ->whereIn('team_id', $myTeams->pluck('id'))
                    ->with('team')
                    ->get();
            } else {
                $myEntries = CompetitionEntry::where('competition_id', $competition->id)
                    ->where('user_id', $user->id)
                    ->get();
            }
        }

        return view('competitions.show', compact(
            'competition', 'entries', 'rounds', 'roundNames', 'canManage', 'canRegister', 'myTeams', 'myEntries'
        ));
    }
}
