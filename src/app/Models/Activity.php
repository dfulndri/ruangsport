<?php

namespace App\Models;

use App\Enums\ActivityStatus;
use App\Enums\ParticipantStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'sport_id', 'club_id', 'organizer_id',
        'venue_id', 'location_text', 'starts_at', 'ends_at', 'quota', 'fee', 'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'quota' => 'integer',
            'fee' => 'integer',
            'status' => ActivityStatus::class,
        ];
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ActivityParticipant::class);
    }

    /** Peserta yang memakai kuota (terdaftar atau sudah hadir). */
    public function confirmedParticipants(): HasMany
    {
        return $this->participants()->whereIn('status', [
            ParticipantStatus::Registered->value,
            ParticipantStatus::Attended->value,
            ParticipantStatus::NoShow->value,
        ]);
    }

    public function waitlist(): HasMany
    {
        return $this->participants()
            ->where('status', ParticipantStatus::Waitlisted->value)
            ->orderBy('registered_at')
            ->orderBy('id');
    }

    public function isFull(): bool
    {
        return $this->quota !== null && $this->confirmedParticipants()->count() >= $this->quota;
    }
}
