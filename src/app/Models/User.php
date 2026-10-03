<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isVenueOwner(): bool
    {
        return $this->role === UserRole::VenueOwner;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function sports(): BelongsToMany
    {
        return $this->belongsToMany(Sport::class, 'user_sports')->withTimestamps();
    }

    /** Keanggotaan klub (termasuk status & role di klub). */
    public function clubMemberships(): HasMany
    {
        return $this->hasMany(ClubMember::class);
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'club_members')
            ->using(ClubMember::class)
            ->withPivot(['role', 'status', 'joined_at'])
            ->withTimestamps();
    }

    public function ownedClubs(): HasMany
    {
        return $this->hasMany(Club::class, 'owner_id');
    }

    public function organizedActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'organizer_id');
    }

    public function activityParticipations(): HasMany
    {
        return $this->hasMany(ActivityParticipant::class);
    }

    public function organizedCompetitions(): HasMany
    {
        return $this->hasMany(Competition::class, 'organizer_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function ownedVenues(): HasMany
    {
        return $this->hasMany(Venue::class, 'owner_id');
    }

    /** True bila user owner/manager (disetujui) di klub tertentu. */
    public function canManageClub(Club|int $club): bool
    {
        $clubId = $club instanceof Club ? $club->id : $club;

        return $this->clubMemberships()
            ->where('club_id', $clubId)
            ->where('status', 'approved')
            ->whereIn('role', ['owner', 'manager'])
            ->exists();
    }
}
