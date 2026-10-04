<?php

namespace App\Services\Club;

use App\Enums\ClubMemberRole;
use App\Enums\ClubMemberStatus;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\User;
use App\Support\Slug;
use Illuminate\Support\Facades\DB;

class ClubService
{
    /** Buat klub baru; pembuatnya otomatis menjadi owner (disetujui). */
    public function create(User $owner, array $data): Club
    {
        return DB::transaction(function () use ($owner, $data) {
            $club = Club::create($data + [
                'slug' => Slug::unique(Club::class, $data['name']),
                'owner_id' => $owner->id,
                'is_verified' => false,
            ]);

            ClubMember::create([
                'club_id' => $club->id,
                'user_id' => $owner->id,
                'role' => ClubMemberRole::Owner,
                'status' => ClubMemberStatus::Approved,
                'joined_at' => now(),
            ]);

            return $club;
        });
    }

    public function update(Club $club, array $data): Club
    {
        if ($club->name !== $data['name']) {
            $data['slug'] = Slug::unique(Club::class, $data['name'], $club->id);
        }

        $club->update($data);

        return $club;
    }

    /** Setujui, tolak, atau ubah peran anggota. */
    public function updateMember(ClubMember $membership, array $data): void
    {
        if ($membership->role === ClubMemberRole::Owner) {
            throw new \DomainException('Pemilik klub tidak dapat diubah.');
        }

        $update = [];

        if (isset($data['status'])) {
            $update['status'] = $data['status'];
            if ($data['status'] === ClubMemberStatus::Approved->value && ! $membership->joined_at) {
                $update['joined_at'] = now();
            }
        }

        if (isset($data['role'])) {
            $update['role'] = $data['role'];
        }

        $membership->update($update);
    }
}
