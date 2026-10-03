<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('sport_id')->constrained();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('organizer_id')->constrained('users');
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location_text')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->unsignedInteger('quota')->nullable();   // null = tanpa batas
            $table->unsignedInteger('fee')->default(0);     // rupiah, hanya informasi (tanpa pembayaran di MVP)
            $table->string('status', 20)->default('draft'); // draft | open | closed | finished | cancelled
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'starts_at']);
        });

        Schema::create('activity_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // registered | waitlisted | cancelled | attended | no_show
            $table->string('status', 20)->default('registered');
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();

            $table->unique(['activity_id', 'user_id']);
            $table->index(['activity_id', 'status', 'registered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_participants');
        Schema::dropIfExists('activities');
    }
};
