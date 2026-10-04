<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Comment;
use App\Models\Competition;
use App\Models\CompetitionEntry;
use App\Models\Location;
use App\Models\Post;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use App\Services\Club\ClubService;
use App\Support\Slug;
use Illuminate\Database\Seeder;

/** Data contoh. Semua akun memakai kata sandi: password */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $sports = Sport::orderBy('id')->get();

        if ($sports->isEmpty()) {
            $this->command?->warn('Tidak ada data olahraga. Jalankan SportSeeder lebih dulu.');

            return;
        }

        $location = Location::whereNotNull('parent_id')->first() ?? Location::first();

        $make = fn (string $name, string $email, string $role) => User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => 'password', 'role' => $role, 'is_active' => true]
        );

        $admin = $make('Admin Ruangsport', 'admin@ruangsport.test', 'admin');
        $venueOwner = $make('Pemilik Venue', 'venue@ruangsport.test', 'venue_owner');
        $organizer = $make('Organizer Demo', 'organizer@ruangsport.test', 'member');
        $member = $make('Member Demo', 'member@ruangsport.test', 'member');
        $players = collect(range(1, 3))->map(fn ($i) => $make('Pemain '.$i, 'pemain'.$i.'@ruangsport.test', 'member'));

        if (Club::exists()) {
            $this->command?->info('Data klub sudah ada, seeder demo dilewati (akun tetap dipastikan ada).');

            return;
        }

        $individual = $sports->first(fn ($s) => in_array($s->participation_type->value, ['individual', 'both'], true)) ?? $sports->first();
        $primary = $sports->first();

        // Klub: organizer menjadi owner; member sudah disetujui, satu pemain masih menunggu.
        $club = app(ClubService::class)->create($organizer, [
            'name' => 'Komunitas '.$primary->name.' Tangerang',
            'sport_id' => $primary->id,
            'location_id' => $location?->id,
            'address' => 'Titik kumpul: gerbang utama',
            'description' => 'Komunitas santai untuk semua level. Latihan rutin tiap akhir pekan.',
        ]);

        ClubMember::create(['club_id' => $club->id, 'user_id' => $member->id, 'role' => 'member', 'status' => 'approved', 'joined_at' => now()]);
        ClubMember::create(['club_id' => $club->id, 'user_id' => $players[0]->id, 'role' => 'member', 'status' => 'pending']);

        // Venue
        $venue = Venue::create([
            'name' => 'GOR Serbaguna Contoh',
            'slug' => Slug::unique(Venue::class, 'GOR Serbaguna Contoh'),
            'owner_id' => $venueOwner->id,
            'location_id' => $location?->id,
            'address' => 'Jl. Contoh No. 1',
            'description' => 'Lapangan indoor dengan lantai standar dan ruang ganti bersih.',
            'opening_hours' => ['Senin - Jumat' => '08.00 - 22.00', 'Sabtu - Minggu' => '07.00 - 23.00'],
        ]);
        $venue->sports()->sync($sports->take(2)->pluck('id')->all());
        foreach (['Parkir luas', 'Kantin', 'Toilet', 'Musala'] as $facility) {
            $venue->facilities()->create(['name' => $facility]);
        }

        // Aktivitas
        foreach ([
            ['Latihan rutin Sabtu pagi', 3, 8],
            ['Fun game Minggu sore', 4, 2],
            ['Sesi pemula', 10, 20],
        ] as [$title, $days, $quota]) {
            Activity::create([
                'title' => $title,
                'slug' => Slug::unique(Activity::class, $title),
                'description' => 'Terbuka untuk anggota dan umum. Bawa perlengkapan masing-masing.',
                'sport_id' => $primary->id,
                'club_id' => $club->id,
                'venue_id' => $venue->id,
                'organizer_id' => $organizer->id,
                'starts_at' => now()->addDays($days)->setTime(8, 0),
                'ends_at' => now()->addDays($days)->setTime(10, 0),
                'quota' => $quota,
                'fee' => 0,
                'status' => 'open',
            ]);
        }

        // Kompetisi individu dengan 4 peserta terverifikasi
        $competition = Competition::create([
            'name' => 'Turnamen Terbuka '.$individual->name,
            'slug' => Slug::unique(Competition::class, 'Turnamen Terbuka '.$individual->name),
            'description' => 'Turnamen sistem gugur untuk semua level.',
            'sport_id' => $individual->id,
            'club_id' => $club->id,
            'venue_id' => $venue->id,
            'organizer_id' => $organizer->id,
            'participant_type' => 'individual',
            'format' => 'knockout',
            'max_participants' => 16,
            'registration_open_at' => now()->subDay(),
            'registration_close_at' => now()->addDays(14),
            'starts_at' => now()->addDays(21),
            'rules' => 'Pertandingan sistem gugur. Keputusan wasit mutlak.',
            'status' => 'registration_open',
        ]);

        foreach ($players->push($member) as $user) {
            CompetitionEntry::create([
                'competition_id' => $competition->id,
                'user_id' => $user->id,
                'status' => 'verified',
                'verified_by' => $organizer->id,
                'verified_at' => now(),
            ]);
        }

        // Komunitas
        $post = Post::create(['user_id' => $member->id, 'body' => 'Ada yang mau sparring akhir pekan ini?']);
        Comment::create(['post_id' => $post->id, 'user_id' => $organizer->id, 'body' => 'Ikut sesi Sabtu pagi saja, masih ada kuota!']);

        $this->command?->info('Akun demo (kata sandi: password): admin@, venue@, organizer@, member@ruangsport.test');
    }
}
