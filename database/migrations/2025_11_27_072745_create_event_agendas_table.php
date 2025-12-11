<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_agendas', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel events
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            
            $table->string('title'); // Judul Agenda
            $table->text('description')->nullable(); // Deskripsi singkat
            $table->dateTime('start_time'); // Waktu mulai
            $table->dateTime('end_time'); // Waktu selesai
            $table->string('location')->nullable(); // Lokasi spesifik (misal: Hall A, Room 1)
            $table->string('speaker')->nullable(); // Pembicara/Pengisi acara
            
            // Fitur tambahan yang kamu minta
            $table->string('link_url')->nullable(); // Link detail (misal: zoom link, atau materi)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_agendas');
    }
};