<?php

namespace App\Http\Controllers\VenueOwner;

use App\Http\Controllers\Controller;
use App\Http\Requests\VenueOwner\VenueRequest;
use App\Models\Location;
use App\Models\Sport;
use App\Models\Venue;
use App\Services\Verification\VerificationService;
use App\Support\Slug;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class VenueController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin melihat semua venue; venue owner hanya miliknya.
        $venues = Venue::query()
            ->with(['location', 'sports'])
            ->when(! $user->isAdmin(), fn ($q) => $q->where('owner_id', $user->id))
            ->orderBy('name')
            ->get();

        return view('venue-owner.venues.index', compact('venues'));
    }

    public function create()
    {
        Gate::authorize('create', Venue::class);

        return view('venue-owner.venues.create', $this->formData());
    }

    public function store(VenueRequest $request): RedirectResponse
    {
        Gate::authorize('create', Venue::class);

        DB::transaction(function () use ($request) {
            $data = $request->validated();

            $venue = Venue::create([
                'name' => $data['name'],
                'slug' => Slug::unique(Venue::class, $data['name']),
                'owner_id' => $request->user()->isAdmin() ? null : $request->user()->id,
                'location_id' => $data['location_id'] ?? null,
                'address' => $data['address'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'description' => $data['description'] ?? null,
                'opening_hours' => $this->parseHours($data['opening_hours'] ?? null),
            ]);

            $this->syncRelations($venue, $data);
        });

        return redirect()->route('venue-owner.venues.index')->with('success', 'Venue berhasil ditambahkan.');
    }

    public function edit(Venue $venue)
    {
        Gate::authorize('update', $venue);

        $venue->load(['sports', 'facilities']);

        return view('venue-owner.venues.edit', $this->formData() + [
            'venue' => $venue,
            'facilitiesText' => $venue->facilities->pluck('name')->implode("\n"),
            'hoursText' => collect($venue->opening_hours ?? [])
                ->map(fn ($hours, $day) => $day.': '.$hours)
                ->implode("\n"),
        ]);
    }

    public function update(VenueRequest $request, Venue $venue): RedirectResponse
    {
        Gate::authorize('update', $venue);

        DB::transaction(function () use ($request, $venue) {
            $data = $request->validated();

            $update = [
                'name' => $data['name'],
                'location_id' => $data['location_id'] ?? null,
                'address' => $data['address'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'description' => $data['description'] ?? null,
                'opening_hours' => $this->parseHours($data['opening_hours'] ?? null),
            ];

            if ($venue->name !== $data['name']) {
                $update['slug'] = Slug::unique(Venue::class, $data['name'], $venue->id);
            }

            $venue->update($update);
            $this->syncRelations($venue, $data);
        });

        return redirect()->route('venue-owner.venues.index')->with('success', 'Venue diperbarui.');
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        Gate::authorize('delete', $venue);

        $venue->delete();

        return redirect()->route('venue-owner.venues.index')->with('success', 'Venue dihapus.');
    }

    public function requestVerification(Venue $venue, VerificationService $service): RedirectResponse
    {
        Gate::authorize('update', $venue);

        try {
            $service->request($venue, auth()->user());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pengajuan verifikasi dikirim. Admin akan meninjaunya.');
    }

    private function formData(): array
    {
        return [
            'sports' => Sport::orderBy('name')->get(),
            'locations' => Location::whereNull('parent_id')->with('children')->orderBy('name')->get(),
        ];
    }

    private function syncRelations(Venue $venue, array $data): void
    {
        $venue->sports()->sync($data['sports'] ?? []);

        $venue->facilities()->delete();

        foreach ($this->lines($data['facilities'] ?? null) as $name) {
            $venue->facilities()->create(['name' => $name]);
        }
    }

    /** @return string[] */
    private function lines(?string $text): array
    {
        return collect(preg_split('/\R/', (string) $text))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    /** "Senin: 08.00-22.00" per baris -> ['Senin' => '08.00-22.00']. */
    private function parseHours(?string $text): ?array
    {
        $hours = [];

        foreach ($this->lines($text) as $line) {
            [$day, $time] = array_pad(explode(':', $line, 2), 2, '');
            $day = trim($day);

            if ($day !== '') {
                $hours[$day] = trim($time);
            }
        }

        return $hours ?: null;
    }
}
