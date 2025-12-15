<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_programmes', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel events
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            
            $table->string('title'); // Judul Program
            $table->text('description')->nullable(); // Deskripsi
            $table->dateTime('start_time'); // Waktu mulai
            $table->dateTime('end_time'); // Waktu selesai
            $table->string('location')->nullable(); // Lokasi
            $table->string('speaker')->nullable(); // Pengisi Acara
            $table->string('link_url')->nullable(); // Link Detail
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_programmes');
    }
};