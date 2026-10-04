<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;

class VenuePolicy extends BasePolicy
{
    /** Venue owner membuat venue (admin sudah diizinkan lewat BasePolicy). */
    public function create(User $user): bool
    {
        return $user->isVenueOwner();
    }

    public function update(User $user, Venue $venue): bool
    {
        return $venue->owner_id === $user->id;
    }

    public function delete(User $user, Venue $venue): bool
    {
        return $venue->owner_id === $user->id;
    }
}
