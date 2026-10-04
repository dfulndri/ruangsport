<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\EntryStatus;
use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CompetitionEntryController extends Controller
{
    /** Verifikasi atau tolak peserta. */
    public function update(Request $request, Competition $competition, int $entry): RedirectResponse
    {
        Gate::authorize('manageEntries', $competition);

        $data = $request->validate([
            'status' => ['required', Rule::in([EntryStatus::Verified->value, EntryStatus::Rejected->value])],
        ]);

        if ($competition->matches()->exists()) {
            return back()->with('error', 'Bracket sudah dibuat, peserta tidak dapat diubah lagi.');
        }

        $row = $competition->entries()->findOrFail($entry);

        $row->update([
            'status' => $data['status'],
            'verified_by' => $data['status'] === EntryStatus::Verified->value ? $request->user()->id : null,
            'verified_at' => $data['status'] === EntryStatus::Verified->value ? now() : null,
        ]);

        return back()->with('success', 'Status peserta diperbarui.');
    }
}
