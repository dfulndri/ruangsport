<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy extends BasePolicy
{
    /** Semua member boleh membuat tim (untuk kompetisi beregu). */
    public function create(User $user): bool
    {
        return true;
    }

    /** Pembuat tim atau kapten tim: ubah tim dan kelola anggota. */
    public function update(User $user, Team $team): bool
    {
        return $team->created_by === $user->id
            || $team->members()
                ->where('users.id', $user->id)
                ->wherePivot('role', 'captain')
                ->exists();
    }

    public function delete(User $user, Team $team): bool
    {
        return $team->created_by === $user->id;
    }
}
