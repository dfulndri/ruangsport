<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->nullable()->constrained()->cascadeOnDelete(); // null = feed umum
            $table->text('body');
            $table->string('image')->nullable();
            $table->timestamps();

            $table->index(['club_id', 'created_at']);
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        // Verifikasi polimorfik: Club, Venue, atau User (organizer)
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('verifiable');
            $table->foreignId('requested_by')->constrained('users');
            $table->string('status', 20)->default('pending'); // pending | approved | rejected
            $table->text('notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifications');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('posts');
    }
};
