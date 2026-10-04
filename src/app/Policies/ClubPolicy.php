<?php

namespace App\Policies;

use App\Models\Club;
use App\Models\User;

class ClubPolicy extends BasePolicy
{
    /** Semua member boleh membuat klub (otomatis menjadi owner, lalu diverifikasi admin). */
    public function create(User $user): bool
    {
        return true;
    }

    /** Ubah profil klub, kelola anggota, dan buat kegiatan atas nama klub. */
    public function update(User $user, Club $club): bool
    {
        return $this->managesClub($user, $club->id);
    }

    public function manageMembers(User $user, Club $club): bool
    {
        return $this->managesClub($user, $club->id);
    }

    /** Hanya pemilik klub yang boleh menghapus klub. */
    public function delete(User $user, Club $club): bool
    {
        return $club->owner_id === $user->id;
    }
}
