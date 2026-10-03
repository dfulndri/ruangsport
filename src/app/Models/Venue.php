<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'owner_id', 'location_id', 'address',
        'latitude', 'longitude', 'description', 'opening_hours', 'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'opening_hours' => 'array',
            'is_verified' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function sports(): BelongsToMany
    {
        return $this->belongsToMany(Sport::class, 'venue_sport');
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(VenueFacility::class);
    }

    public function verifications(): MorphMany
    {
        return $this->morphMany(Verification::class, 'verifiable');
    }
}
