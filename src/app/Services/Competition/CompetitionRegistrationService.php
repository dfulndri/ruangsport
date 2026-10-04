<?php

namespace App\Services\Competition;

use App\Enums\CompetitionStatus;
use App\Enums\EntryStatus;
use App\Models\Competition;
use App\Models\CompetitionEntry;
use App\Models\Team;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Pendaftaran peserta kompetisi: individu (user) atau tim, sesuai participant_type kompetisi.
 */
class CompetitionRegistrationService
{
    public function register(Competition $competition, User $user, ?int $teamId = null): CompetitionEntry
    {
        return DB::transaction(function () use ($competition, $user, $teamId) {
            $competition = Competition::whereKey($competition->id)->lockForUpdate()->firstOrFail();

            if ($competition->status !== CompetitionStatus::RegistrationOpen) {
                throw new DomainException('Pendaftaran kompetisi ini sedang tidak dibuka.');
            }

            $column = 'user_id';
            $value = $user->id;

            if ($competition->isTeam()) {
                if (! $teamId) {
                    throw new DomainException('Pilih tim yang akan didaftarkan.');
                }

                $team = Team::findOrFail($teamId);

                if ((int) $team->sport_id !== (int) $competition->sport_id) {
                    throw new DomainException('Cabang olahraga tim tidak sesuai dengan kompetisi ini.');
                }

                if (! $this->canManageTeam($team, $user)) {
                    throw new DomainException('Hanya pembuat atau kapten tim yang dapat mendaftarkan tim.');
                }

                $column = 'team_id';
                $value = $team->id;
            }

            $existing = CompetitionEntry::where('competition_id', $competition->id)
                ->where($column, $value)
                ->first();

            if ($existing && in_array($existing->status, [EntryStatus::Pending, EntryStatus::Verified], true)) {
                throw new DomainException('Sudah terdaftar pada kompetisi ini.');
            }

            if ($existing && $existing->status === EntryStatus::Rejected) {
                throw new DomainException('Pendaftaran sebelumnya ditolak oleh penyelenggara.');
            }

            if ($competition->max_participants) {
                $taken = CompetitionEntry::where('competition_id', $competition->id)
                    ->whereIn('status', [EntryStatus::Pending->value, EntryStatus::Verified->value])
                    ->count();

                if ($taken >= $competition->max_participants) {
                    throw new DomainException('Kuota peserta kompetisi sudah penuh.');
                }
            }

            if ($existing) {
                $existing->update(['status' => EntryStatus::Pending, 'verified_by' => null, 'verified_at' => null]);

                return $existing;
            }

            return CompetitionEntry::create([
                'competition_id' => $competition->id,
                $column => $value,
                'status' => EntryStatus::Pending,
            ]);
        });
    }

    public function withdraw(Competition $competition, User $user, ?int $teamId = null): void
    {
        $entry = $this->findEntry($competition, $user, $teamId);

        if (! $entry || ! in_array($entry->status, [EntryStatus::Pending, EntryStatus::Verified], true)) {
            throw new DomainException('Tidak ada pendaftaran aktif untuk dibatalkan.');
        }

        if ($competition->matches()->exists()) {
            throw new DomainException('Bracket sudah dibuat. Hubungi penyelenggara untuk mengundurkan diri.');
        }

        $entry->update(['status' => EntryStatus::Withdrawn]);
    }

    private function findEntry(Competition $competition, User $user, ?int $teamId): ?CompetitionEntry
    {
        if ($competition->isTeam()) {
            $team = $teamId ? Team::find($teamId) : null;

            if (! $team || ! $this->canManageTeam($team, $user)) {
                throw new DomainException('Tim tidak valid.');
            }

            return CompetitionEntry::where('competition_id', $competition->id)->where('team_id', $team->id)->first();
        }

        return CompetitionEntry::where('competition_id', $competition->id)->where('user_id', $user->id)->first();
    }

    private function canManageTeam(Team $team, User $user): bool
    {
        return $team->created_by === $user->id
            || $team->members()->where('users.id', $user->id)->wherePivot('role', 'captain')->exists();
    }
}
