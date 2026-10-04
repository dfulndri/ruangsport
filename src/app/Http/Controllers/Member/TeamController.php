<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\TeamRequest;
use App\Models\Club;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $teams = Team::query()
            ->with(['sport', 'club', 'members'])
            ->where(fn ($q) => $q->where('created_by', $user->id)
                ->orWhereHas('members', fn ($m) => $m->where('users.id', $user->id)))
            ->orderBy('name')
            ->get();

        $sports = Sport::whereIn('participation_type', ['team', 'both'])->orderBy('name')->get();
        $clubs = Club::whereIn('id', $user->clubMemberships()->where('status', 'approved')->pluck('club_id'))->orderBy('name')->get();

        return view('member.teams.index', compact('teams', 'sports', 'clubs'));
    }

    public function store(TeamRequest $request): RedirectResponse
    {
        Gate::authorize('create', Team::class);

        DB::transaction(function () use ($request) {
            $team = Team::create($request->validated() + ['created_by' => $request->user()->id]);
            $team->members()->attach($request->user()->id, ['role' => 'captain']);
        });

        return back()->with('success', 'Tim berhasil dibuat. Anda menjadi kapten.');
    }

    public function destroy(Team $team): RedirectResponse
    {
        Gate::authorize('delete', $team);

        if ($team->competitionEntries()->exists()) {
            return back()->with('error', 'Tim sudah terdaftar di kompetisi dan tidak dapat dihapus.');
        }

        $team->delete();

        return back()->with('success', 'Tim dihapus.');
    }

    /** Tambah anggota berdasarkan email akun yang sudah terdaftar. */
    public function addMember(Request $request, Team $team): RedirectResponse
    {
        Gate::authorize('update', $team);

        $data = $request->validate(['email' => ['required', 'email']], [
            'email.required' => 'Email anggota wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return back()->with('error', 'Akun dengan email tersebut belum terdaftar di Ruangsport.');
        }

        if ($team->members()->where('users.id', $user->id)->exists()) {
            return back()->with('info', 'Pengguna tersebut sudah menjadi anggota tim.');
        }

        $team->members()->attach($user->id, ['role' => 'member']);

        return back()->with('success', $user->name.' ditambahkan ke tim.');
    }

    public function removeMember(Team $team, User $user): RedirectResponse
    {
        Gate::authorize('update', $team);

        if ($team->created_by === $user->id) {
            return back()->with('error', 'Pembuat tim tidak dapat dikeluarkan.');
        }

        $team->members()->detach($user->id);

        return back()->with('success', 'Anggota dikeluarkan dari tim.');
    }
}
