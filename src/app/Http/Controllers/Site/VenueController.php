<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index(Request $request)
    {
        $venues = Venue::query()
            ->with(['location', 'sports'])
            ->when($request->filled('sport'), fn ($q) => $q->whereHas('sports', fn ($s) => $s->where('sports.id', $request->integer('sport'))))
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q').'%'))
            ->orderByDesc('is_verified')
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        $sports = Sport::orderBy('name')->get();

        return view('venues.index', compact('venues', 'sports'));
    }

    public function show(Venue $venue)
    {
        $venue->load(['location', 'sports', 'facilities', 'owner']);

        return view('venues.show', compact('venue'));
    }
}
