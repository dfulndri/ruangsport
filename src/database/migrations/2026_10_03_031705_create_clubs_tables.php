<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('sport_id')->constrained();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('address')->nullable();
            $table->foreignId('owner_id')->constrained('users');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sport_id', 'location_id']);
        });

        Schema::create('club_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20)->default('member');     // owner | manager | member
            $table->string('status', 20)->default('pending');  // pending | approved | rejected
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['club_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_members');
        Schema::dropIfExists('clubs');
    }
};
