<?php

namespace App\Models;

use App\Enums\MatchStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionMatch extends Model
{
    protected $table = 'competition_matches';

    protected $fillable = [
        'competition_id', 'round', 'match_number', 'entry_a_id', 'entry_b_id',
        'score_a', 'score_b', 'winner_entry_id', 'is_bye',
        'next_match_id', 'next_slot', 'scheduled_at', 'court', 'status',
    ];

    protected function casts(): array
    {
        return [
            'round' => 'integer',
            'match_number' => 'integer',
            'score_a' => 'integer',
            'score_b' => 'integer',
            'is_bye' => 'boolean',
            'scheduled_at' => 'datetime',
            'status' => MatchStatus::class,
        ];
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function entryA(): BelongsTo
    {
        return $this->belongsTo(CompetitionEntry::class, 'entry_a_id');
    }

    public function entryB(): BelongsTo
    {
        return $this->belongsTo(CompetitionEntry::class, 'entry_b_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(CompetitionEntry::class, 'winner_entry_id');
    }

    /** Match babak berikutnya yang menerima pemenang match ini. */
    public function nextMatch(): BelongsTo
    {
        return $this->belongsTo(CompetitionMatch::class, 'next_match_id');
    }

    /** Match babak sebelumnya yang pemenangnya masuk ke match ini. */
    public function previousMatches(): HasMany
    {
        return $this->hasMany(CompetitionMatch::class, 'next_match_id');
    }
}
