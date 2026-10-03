<?php

namespace App\Models;

use App\Enums\ClubMemberRole;
use App\Enums\ClubMemberStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Pivot club_members dengan primary key sendiri (id), jadi incrementing = true.
 */
class ClubMember extends Pivot
{
    protected $table = 'club_members';

    public $incrementing = true;

    protected $fillable = ['club_id', 'user_id', 'role', 'status', 'joined_at'];

    protected function casts(): array
    {
        return [
            'role' => ClubMemberRole::class,
            'status' => ClubMemberStatus::class,
            'joined_at' => 'datetime',
        ];
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
