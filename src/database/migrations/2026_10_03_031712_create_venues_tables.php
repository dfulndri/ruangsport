<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('description')->nullable();
            $table->json('opening_hours')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('venue_sport', function (Blueprint $table) {
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sport_id')->constrained()->cascadeOnDelete();

            $table->primary(['venue_id', 'sport_id']);
        });

        Schema::create('venue_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_facilities');
        Schema::dropIfExists('venue_sport');
        Schema::dropIfExists('venues');
    }
};
