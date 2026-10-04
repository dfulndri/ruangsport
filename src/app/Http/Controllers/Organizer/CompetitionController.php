<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\EntryStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organizer\CompetitionRequest;
use App\Models\Competition;
use App\Models\Sport;
use App\Models\Venue;
use App\Support\Bracket;
use App\Support\Slug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class CompetitionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $clubIds = DashboardController::managedClubs($user)->pluck('id');

        $competitions = Competition::query()
            ->with('sport')
            ->withCount([
                'entries as pending_count' => fn ($q) => $q->where('status', EntryStatus::Pending->value),
                'entries as verified_count' => fn ($q) => $q->where('status', EntryStatus::Verified->value),
            ])
            ->when(! $user->isAdmin(), fn ($q) => $q->where(
                fn ($w) => $w->where('organizer_id', $user->id)->orWhereIn('club_id', $clubIds)
            ))
            ->latest()
            ->paginate(15);

        return view('organizer.competitions.index', compact('competitions'));
    }

    public function create()
    {
        Gate::authorize('create', Competition::class);

        return view('organizer.competitions.create', $this->formData());
    }

    public function store(CompetitionRequest $request): RedirectResponse
    {
        Gate::authorize('create', Competition::class);

        $data = $request->validated();

        $competition = Competition::create($data + [
            'slug' => Slug::unique(Competition::class, $data['name']),
            'organizer_id' => $request->user()->id,
            'format' => 'knockout',
        ]);

        return redirect()->route('organizer.competitions.manage', $competition->slug)
            ->with('success', 'Kompetisi dibuat. Buka pendaftaran saat siap menerima peserta.');
    }

    public function edit(Competition $competition)
    {
        Gate::authorize('update', $competition);

        return view('organizer.competitions.edit', $this->formData() + [
            'competition' => $competition,
            'locked' => $competition->entries()->exists(),
        ]);
    }

    public function update(CompetitionRequest $request, Competition $competition): RedirectResponse
    {
        Gate::authorize('update', $competition);

        $data = $request->validated();

        // Setelah ada peserta, olahraga dan jenis peserta tidak boleh diubah.
        if ($competition->entries()->exists()) {
            unset($data['sport_id'], $data['participant_type']);
        }

        if ($competition->name !== $data['name']) {
            $data['slug'] = Slug::unique(Competition::class, $data['name'], $competition->id);
        }

        $competition->update($data);

        return redirect()->route('organizer.competitions.manage', $competition->fresh()->slug)
            ->with('success', 'Kompetisi diperbarui.');
    }

    public function destroy(Competition $competition): RedirectResponse
    {
        Gate::authorize('delete', $competition);

        $competition->delete();

        return redirect()->route('organizer.competitions.index')->with('success', 'Kompetisi dihapus.');
    }

    /** Halaman kelola: peserta, bracket, dan input skor. */
    public function manage(Competition $competition)
    {
        Gate::authorize('manageEntries', $competition);

        $competition->load(['sport', 'venue']);

        $entries = $competition->entries()
            ->with(['user', 'team.members'])
            ->orderByRaw("case status when 'pending' then 0 when 'verified' then 1 else 2 end")
            ->orderBy('seed')
            ->orderBy('id')
            ->get();

        $matches = $competition->matches()
            ->with(['entryA.user', 'entryA.team', 'entryB.user', 'entryB.team'])
            ->get();

        $rounds = $matches->groupBy('round');
        $lastRound = (int) $rounds->keys()->max();
        $roundNames = $rounds->keys()->mapWithKeys(fn ($r) => [$r => Bracket::roundName($r, $lastRound)]);

        return view('organizer.competitions.manage', compact('competition', 'entries', 'rounds', 'roundNames'));
    }

    private function formData(): array
    {
        return [
            'sports' => Sport::orderBy('name')->get(),
            'clubs' => DashboardController::managedClubs(auth()->user())->get(),
            'venues' => Venue::orderBy('name')->get(),
        ];
    }
}
