<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // role global: member | venue_owner | admin (hak organizer diturunkan dari club_members)
            $table->string('role', 20)->default('member')->after('password');
            $table->boolean('is_active')->default(true)->after('role');
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('phone', 30)->nullable();
            $table->string('gender', 10)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('user_sports', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sport_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['user_id', 'sport_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sports');
        Schema::dropIfExists('user_profiles');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active']);
        });
    }
};
