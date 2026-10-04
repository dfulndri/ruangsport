<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Services\Competition\ScoreRecorder;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MatchController extends Controller
{
    /** Atur jadwal/lapangan dan/atau input skor satu pertandingan. */
    public function update(Request $request, Competition $competition, int $match, ScoreRecorder $recorder): RedirectResponse
    {
        Gate::authorize('manageMatches', $competition);

        $data = $request->validate([
            'score_a' => ['nullable', 'integer', 'min:0', 'max:999', 'required_with:score_b'],
            'score_b' => ['nullable', 'integer', 'min:0', 'max:999', 'required_with:score_a'],
            'scheduled_at' => ['nullable', 'date'],
            'court' => ['nullable', 'string', 'max:100'],
        ]);

        $row = $competition->matches()->findOrFail($match);

        $row->update([
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'court' => $data['court'] ?? null,
        ]);

        if (isset($data['score_a'], $data['score_b'])) {
            try {
                $recorder->record($row, (int) $data['score_a'], (int) $data['score_b']);
            } catch (DomainException $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        return back()->with('success', 'Pertandingan diperbarui.');
    }
}
