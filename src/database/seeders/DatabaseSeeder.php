<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SportSeeder::class,
            LocationSeeder::class,
        ]);

        // Akun admin awal untuk pengembangan. Ganti password-nya sebelum produksi.
        User::updateOrCreate(
            ['email' => 'admin@ruangsport.test'],
            [
                'name' => 'Admin Ruangsport',
                'password' => 'password', // di-hash otomatis oleh cast pada model User
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ],
        );
    }
}
