<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'logo', 'description', 'sport_id',
        'location_id', 'address', 'owner_id', 'is_verified',
    ];

    protected function casts(): array
    {
        return ['is_verified' => 'boolean'];
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(ClubMember::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'club_members')
            ->using(ClubMember::class)
            ->withPivot(['role', 'status', 'joined_at'])
            ->withTimestamps();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function verifications(): MorphMany
    {
        return $this->morphMany(Verification::class, 'verifiable');
    }
}
