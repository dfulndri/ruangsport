<?php

namespace App\Services\Verification;

use App\Enums\VerificationStatus;
use App\Models\Club;
use App\Models\User;
use App\Models\Venue;
use App\Models\Verification;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VerificationService
{
    /** Ajukan verifikasi untuk Club atau Venue. */
    public function request(Model $verifiable, User $by): Verification
    {
        if (! $verifiable instanceof Club && ! $verifiable instanceof Venue) {
            throw new DomainException('Jenis data ini tidak dapat diverifikasi.');
        }

        if ($verifiable->is_verified) {
            throw new DomainException('Sudah terverifikasi.');
        }

        $pending = $verifiable->verifications()
            ->where('status', VerificationStatus::Pending->value)
            ->exists();

        if ($pending) {
            throw new DomainException('Pengajuan verifikasi masih menunggu peninjauan admin.');
        }

        return $verifiable->verifications()->create([
            'requested_by' => $by->id,
            'status' => VerificationStatus::Pending,
        ]);
    }

    /** Admin menyetujui atau menolak pengajuan. */
    public function review(Verification $verification, User $admin, bool $approve, ?string $notes = null): void
    {
        DB::transaction(function () use ($verification, $admin, $approve, $notes) {
            if ($verification->status !== VerificationStatus::Pending) {
                throw new DomainException('Pengajuan ini sudah ditinjau.');
            }

            $verification->update([
                'status' => $approve ? VerificationStatus::Approved : VerificationStatus::Rejected,
                'notes' => $notes,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $target = $verification->verifiable;

            if ($approve && $target) {
                $target->update(['is_verified' => true]);
            }
        });
    }
}
