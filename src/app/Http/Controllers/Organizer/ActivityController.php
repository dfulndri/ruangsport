<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\ParticipantStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organizer\ActivityRequest;
use App\Models\Activity;
use App\Models\Sport;
use App\Models\Venue;
use App\Support\Slug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ActivityController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $clubIds = DashboardController::managedClubs($user)->pluck('id');

        $activities = Activity::query()
            ->with(['sport', 'club'])
            ->withCount(['participants as confirmed_count' => fn ($q) => $q->whereIn('status', [
                ParticipantStatus::Registered->value,
                ParticipantStatus::Attended->value,
                ParticipantStatus::NoShow->value,
            ])])
            ->when(! $user->isAdmin(), fn ($q) => $q->where(
                fn ($w) => $w->where('organizer_id', $user->id)->orWhereIn('club_id', $clubIds)
            ))
            ->orderByDesc('starts_at')
            ->paginate(15);

        return view('organizer.activities.index', compact('activities'));
    }

    public function create()
    {
        Gate::authorize('create', Activity::class);

        return view('organizer.activities.create', $this->formData());
    }

    public function store(ActivityRequest $request): RedirectResponse
    {
        Gate::authorize('create', Activity::class);

        $data = $request->validated();
        $data['fee'] = $data['fee'] ?? 0;

        Activity::create($data + [
            'slug' => Slug::unique(Activity::class, $data['title']),
            'organizer_id' => $request->user()->id,
        ]);

        return redirect()->route('organizer.activities.index')->with('success', 'Aktivitas berhasil dibuat.');
    }

    public function edit(Activity $activity)
    {
        Gate::authorize('update', $activity);

        return view('organizer.activities.edit', $this->formData() + ['activity' => $activity]);
    }

    public function update(ActivityRequest $request, Activity $activity): RedirectResponse
    {
        Gate::authorize('update', $activity);

        $data = $request->validated();
        $data['fee'] = $data['fee'] ?? 0;

        if ($activity->title !== $data['title']) {
            $data['slug'] = Slug::unique(Activity::class, $data['title'], $activity->id);
        }

        $activity->update($data);

        return redirect()->route('organizer.activities.index')->with('success', 'Aktivitas diperbarui.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        Gate::authorize('delete', $activity);

        $activity->delete();

        return redirect()->route('organizer.activities.index')->with('success', 'Aktivitas dihapus.');
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
