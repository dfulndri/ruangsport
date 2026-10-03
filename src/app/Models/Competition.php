<?php

namespace App\Models;

use App\Enums\CompetitionFormat;
use App\Enums\CompetitionStatus;
use App\Enums\ParticipationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Competition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'sport_id', 'organizer_id', 'club_id', 'venue_id',
        'participant_type', 'format', 'max_participants',
        'registration_open_at', 'registration_close_at', 'starts_at', 'ends_at',
        'rules', 'status',
    ];

    protected function casts(): array
    {
        return [
            'participant_type' => ParticipationType::class,
            'format' => CompetitionFormat::class,
            'status' => CompetitionStatus::class,
            'max_participants' => 'integer',
            'registration_open_at' => 'datetime',
            'registration_close_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(CompetitionEntry::class);
    }

    public function verifiedEntries(): HasMany
    {
        return $this->entries()->where('status', 'verified');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(CompetitionMatch::class)->orderBy('round')->orderBy('match_number');
    }

    public function isIndividual(): bool
    {
        return $this->participant_type === ParticipationType::Individual;
    }

    public function isTeam(): bool
    {
        return $this->participant_type === ParticipationType::Team;
    }
}
