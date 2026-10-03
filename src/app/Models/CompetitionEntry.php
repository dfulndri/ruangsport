<?php

namespace App\Models;

use App\Enums\EntryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Peserta kompetisi: individu (user_id) ATAU tim (team_id).
 */
class CompetitionEntry extends Model
{
    protected $fillable = [
        'competition_id', 'user_id', 'team_id', 'seed',
        'status', 'verified_by', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => EntryStatus::class,
            'seed' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /** Nama yang ditampilkan di bracket, baik individu maupun tim. */
    public function getDisplayNameAttribute(): string
    {
        return $this->team?->name ?? $this->user?->name ?? '-';
    }
}
