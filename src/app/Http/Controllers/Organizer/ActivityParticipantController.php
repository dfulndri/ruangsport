<?php

namespace App\Http\Controllers\Organizer;

use App\Enums\ParticipantStatus;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ActivityParticipantController extends Controller
{
    public function index(Activity $activity)
    {
        Gate::authorize('manageParticipants', $activity);

        $participants = $activity->participants()
            ->with('user')
            ->whereNot('status', ParticipantStatus::Cancelled->value)
            ->orderByRaw("case status when 'waitlisted' then 1 else 0 end")
            ->orderBy('registered_at')
            ->get();

        return view('organizer.activities.participants', compact('activity', 'participants'));
    }

    /** Catat kehadiran: hadir, tidak hadir, atau kembalikan ke terdaftar. */
    public function update(Request $request, Activity $activity, int $participant): RedirectResponse
    {
        Gate::authorize('manageParticipants', $activity);

        $data = $request->validate([
            'status' => ['required', Rule::in([
                ParticipantStatus::Attended->value,
                ParticipantStatus::NoShow->value,
                ParticipantStatus::Registered->value,
            ])],
        ]);

        $row = $activity->participants()->findOrFail($participant);

        if ($row->status === ParticipantStatus::Waitlisted) {
            return back()->with('error', 'Peserta daftar tunggu belum bisa dicatat kehadirannya.');
        }

        $row->update([
            'status' => $data['status'],
            'checked_in_at' => $data['status'] === ParticipantStatus::Attended->value ? now() : null,
        ]);

        return back()->with('success', 'Kehadiran dicatat.');
    }
}
