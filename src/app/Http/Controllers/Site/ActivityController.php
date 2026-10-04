<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Sport;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    private const CONFIRMED = ['registered', 'attended', 'no_show'];

    public function index(Request $request)
    {
        $activities = Activity::query()
            ->with(['sport', 'club'])
            ->withCount(['participants as confirmed_count' => fn ($q) => $q->whereIn('status', self::CONFIRMED)])
            ->where('status', '!=', 'draft')
            ->where('starts_at', '>=', now()->startOfDay())
            ->when($request->filled('sport'), fn ($q) => $q->where('sport_id', $request->integer('sport')))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->orderBy('starts_at')
            ->paginate(10)
            ->withQueryString();

        $sports = Sport::orderBy('name')->get();

        return view('activities.index', compact('activities', 'sports'));
    }

    public function show(Activity $activity)
    {
        $user = auth()->user();

        // Draft hanya terlihat oleh pengelolanya.
        abort_if($activity->status->value === 'draft' && ! ($user && $user->can('update', $activity)), 404);

        $activity->load(['sport', 'club', 'organizer', 'venue']);

        $confirmedCount = $activity->participants()->whereIn('status', self::CONFIRMED)->count();

        $participants = $activity->participants()
            ->with('user')
            ->whereIn('status', self::CONFIRMED)
            ->orderBy('registered_at')
            ->limit(40)
            ->get();

        $mine = $user ? $activity->participants()->where('user_id', $user->id)->first() : null;

        return view('activities.show', compact('activity', 'confirmedCount', 'participants', 'mine'));
    }
}
