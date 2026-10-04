<?php

namespace App\Services\Activity;

use App\Models\Activity;
use App\Models\ActivityParticipant;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

/** RSVP aktivitas dengan kuota dan daftar tunggu (waiting list). */
class ActivityRegistrationService
{
    private const ACTIVE = ['registered', 'waitlisted', 'attended', 'no_show'];

    private const CONFIRMED = ['registered', 'attended', 'no_show'];

    public function register(Activity $activity, User $user): ActivityParticipant
    {
        return DB::transaction(function () use ($activity, $user) {
            $activity = Activity::whereKey($activity->id)->lockForUpdate()->firstOrFail();

            if ($activity->status->value !== 'open') {
                throw new DomainException('Pendaftaran aktivitas ini sedang tidak dibuka.');
            }

            if (($activity->ends_at ?? $activity->starts_at)->isPast()) {
                throw new DomainException('Aktivitas ini sudah berlalu.');
            }

            $existing = ActivityParticipant::where('activity_id', $activity->id)->where('user_id', $user->id)->first();

            if ($existing && in_array($existing->status->value, self::ACTIVE, true)) {
                throw new DomainException('Anda sudah terdaftar pada aktivitas ini.');
            }

            $full = $activity->quota && $this->confirmedCount($activity) >= $activity->quota;

            $attributes = [
                'status' => $full ? 'waitlisted' : 'registered',
                'registered_at' => now(),
                'checked_in_at' => null,
            ];

            if ($existing) {
                $existing->update($attributes);

                return $existing;
            }

            return ActivityParticipant::create($attributes + [
                'activity_id' => $activity->id,
                'user_id' => $user->id,
            ]);
        });
    }

    public function cancel(Activity $activity, User $user): void
    {
        DB::transaction(function () use ($activity, $user) {
            $activity = Activity::whereKey($activity->id)->lockForUpdate()->firstOrFail();

            $participant = ActivityParticipant::where('activity_id', $activity->id)->where('user_id', $user->id)->first();

            if (! $participant || ! in_array($participant->status->value, ['registered', 'waitlisted'], true)) {
                throw new DomainException('Tidak ada pendaftaran aktif untuk dibatalkan.');
            }

            $wasRegistered = $participant->status->value === 'registered';
            $participant->update(['status' => 'cancelled']);

            if ($wasRegistered) {
                $this->promoteFromWaitlist($activity);
            }
        });
    }

    /** Satu slot kosong -> peserta daftar tunggu terdepan naik jadi terdaftar. */
    private function promoteFromWaitlist(Activity $activity): void
    {
        if ($activity->quota && $this->confirmedCount($activity) >= $activity->quota) {
            return;
        }

        ActivityParticipant::where('activity_id', $activity->id)
            ->where('status', 'waitlisted')
            ->orderBy('registered_at')
            ->orderBy('id')
            ->first()
            ?->update(['status' => 'registered']);
    }

    private function confirmedCount(Activity $activity): int
    {
        return ActivityParticipant::where('activity_id', $activity->id)->whereIn('status', self::CONFIRMED)->count();
    }
}
