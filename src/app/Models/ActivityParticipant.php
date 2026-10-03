<?php

namespace App\Models;

use App\Enums\ParticipantStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityParticipant extends Model
{
    protected $fillable = ['activity_id', 'user_id', 'status', 'registered_at', 'checked_in_at'];

    protected function casts(): array
    {
        return [
            'status' => ParticipantStatus::class,
            'registered_at' => 'datetime',
            'checked_in_at' => 'datetime',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
