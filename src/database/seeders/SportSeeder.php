<?php

namespace Database\Seeders;

use App\Enums\ParticipationType;
use App\Models\Sport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            ['Futsal', ParticipationType::Team],
            ['Mini Soccer', ParticipationType::Team],
            ['Sepak Bola', ParticipationType::Team],
            ['Basket', ParticipationType::Team],
            ['Bola Voli', ParticipationType::Team],
            ['Badminton', ParticipationType::Both],
            ['Tenis', ParticipationType::Both],
            ['Padel', ParticipationType::Both],
            ['Tenis Meja', ParticipationType::Both],
            ['Lari', ParticipationType::Individual],
            ['Bersepeda', ParticipationType::Individual],
            ['Renang', ParticipationType::Individual],
            ['Yoga & Senam', ParticipationType::Individual],
        ];

        foreach ($sports as [$name, $type]) {
            Sport::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'participation_type' => $type],
            );
        }
    }
}
