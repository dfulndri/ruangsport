<?php

namespace App\Models;

use App\Enums\ParticipationType;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'participation_type'];

    protected function casts(): array
    {
        return ['participation_type' => ParticipationType::class];
    }

    /** Apakah olahraga ini mengizinkan jenis peserta kompetisi tertentu. */
    public function allowsParticipantType(ParticipationType $type): bool
    {
        return $this->participation_type === ParticipationType::Both
            || $this->participation_type === $type;
    }
}
