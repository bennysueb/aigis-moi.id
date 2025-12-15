<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Nama event dalam multi-bahasa
            $table->string('slug')->unique();
            $table->json('description')->nullable(); // Deskripsi dalam multi-bahasa
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->json('venue')->nullable(); // Lokasi/tempat acara
            $table->integer('quota')->default(0); // Kuota pendaftar
            $table->boolean('is_active')->default(false); // Saklar aktivasi pendaftaran
            $table->string('status')->default('upcoming'); // Status: upcoming, ongoing, finished
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
