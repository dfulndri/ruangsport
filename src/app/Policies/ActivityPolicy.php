<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy extends BasePolicy
{
    /** Pengelola (owner/manager) setidaknya satu klub boleh membuat kegiatan. */
    public function create(User $user): bool
    {
        return $this->managesAnyClub($user);
    }

    public function update(User $user, Activity $activity): bool
    {
        return $this->manages($user, $activity);
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $this->manages($user, $activity);
    }

    /** Lihat daftar peserta dan catat kehadiran. */
    public function manageParticipants(User $user, Activity $activity): bool
    {
        return $this->manages($user, $activity);
    }

    /** Pembuat kegiatan, atau pengelola klub penyelenggara. */
    private function manages(User $user, Activity $activity): bool
    {
        return $activity->organizer_id === $user->id
            || $this->managesClub($user, $activity->club_id);
    }
}
