<?php

namespace App\Policies;

use App\Models\User;

/**
 * Dasar semua policy Ruangsport.
 *
 * Aturan global (dijalankan sebelum ability apa pun):
 *  - akun nonaktif ditolak,
 *  - admin diizinkan untuk semua aksi.
 * Mengembalikan null berarti "lanjut ke aturan ability masing-masing".
 */
abstract class BasePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if (! $user->is_active) {
            return false;
        }

        return $user->isAdmin() ? true : null;
    }

    /** User adalah owner/manager (disetujui) di klub tertentu. */
    protected function managesClub(User $user, ?int $clubId): bool
    {
        return $clubId !== null && $user->canManageClub($clubId);
    }

    /** User adalah owner/manager (disetujui) di setidaknya satu klub. */
    protected function managesAnyClub(User $user): bool
    {
        return $user->clubMemberships()
            ->where('status', 'approved')
            ->whereIn('role', ['owner', 'manager'])
            ->exists();
    }
}
