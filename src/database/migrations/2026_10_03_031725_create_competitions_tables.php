<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('sport_id')->constrained();
            $table->foreignId('organizer_id')->constrained('users');
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->string('participant_type', 20);               // individual | team
            $table->string('format', 20)->default('knockout');    // MVP: knockout
            $table->unsignedSmallInteger('max_participants')->nullable();
            $table->dateTime('registration_open_at')->nullable();
            $table->dateTime('registration_close_at')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->text('rules')->nullable();
            // draft | registration_open | ongoing | finished
            $table->string('status', 20)->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'starts_at']);
        });

        // Peserta kompetisi: individu (user_id) ATAU tim (team_id), sesuai competitions.participant_type.
        // Aturan "tepat salah satu terisi" dijaga di lapisan aplikasi (validasi/service).
        Schema::create('competition_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('seed')->nullable();
            // pending | verified | rejected | withdrawn
            $table->string('status', 20)->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['competition_id', 'user_id']);
            $table->unique(['competition_id', 'team_id']);
        });

        Schema::create('competition_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('round');            // 1 = babak pertama
            $table->unsignedSmallInteger('match_number');    // urutan dalam babak
            $table->foreignId('entry_a_id')->nullable()->constrained('competition_entries')->nullOnDelete();
            $table->foreignId('entry_b_id')->nullable()->constrained('competition_entries')->nullOnDelete();
            $table->unsignedSmallInteger('score_a')->nullable();
            $table->unsignedSmallInteger('score_b')->nullable();
            $table->foreignId('winner_entry_id')->nullable()->constrained('competition_entries')->nullOnDelete();
            $table->boolean('is_bye')->default(false);
            $table->foreignId('next_match_id')->nullable()->constrained('competition_matches')->nullOnDelete();
            $table->string('next_slot', 1)->nullable();      // 'a' | 'b'
            $table->dateTime('scheduled_at')->nullable();
            $table->string('court')->nullable();
            // scheduled | ongoing | finished | walkover
            $table->string('status', 20)->default('scheduled');
            $table->timestamps();

            $table->unique(['competition_id', 'round', 'match_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_matches');
        Schema::dropIfExists('competition_entries');
        Schema::dropIfExists('competitions');
    }
};
