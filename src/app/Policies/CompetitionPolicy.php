<?php

namespace App\Policies;

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use App\Models\User;

class CompetitionPolicy extends BasePolicy
{
    public function create(User $user): bool
    {
        return $this->managesAnyClub($user);
    }

    public function update(User $user, Competition $competition): bool
    {
        return $this->manages($user, $competition);
    }

    public function delete(User $user, Competition $competition): bool
    {
        return $this->manages($user, $competition);
    }

    /** Verifikasi peserta (entries). */
    public function manageEntries(User $user, Competition $competition): bool
    {
        return $this->manages($user, $competition);
    }

    /** Buat bracket, atur jadwal, dan input skor. */
    public function manageMatches(User $user, Competition $competition): bool
    {
        return $this->manages($user, $competition);
    }

    /** Pendaftaran peserta: hanya saat pendaftaran dibuka dan dalam rentang waktunya. */
    public function register(User $user, Competition $competition): bool
    {
        if ($competition->status !== CompetitionStatus::RegistrationOpen) {
            return false;
        }

        $now = now();

        return (! $competition->registration_open_at || $competition->registration_open_at->lte($now))
            && (! $competition->registration_close_at || $competition->registration_close_at->gte($now));
    }

    private function manages(User $user, Competition $competition): bool
    {
        return $competition->organizer_id === $user->id
            || $this->managesClub($user, $competition->club_id);
    }
}
