<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            // individual | team | both -> menentukan jenis peserta kompetisi yang diizinkan
            $table->string('participation_type', 20)->default('both');
            $table->timestamps();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 20); // kota | kabupaten | kecamatan
            $table->foreignId('parent_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->timestamps();

            $table->unique(['parent_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
        Schema::dropIfExists('sports');
    }
};
