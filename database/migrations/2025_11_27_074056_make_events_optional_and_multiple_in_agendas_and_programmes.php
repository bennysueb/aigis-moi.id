<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modifikasi Tabel Event Agenda
        Schema::table('event_agendas', function (Blueprint $table) {
            $table->dropForeign(['event_id']); // Hapus foreign key lama
            $table->dropColumn('event_id');    // Hapus kolom event_id
        });

        // Buat tabel pivot untuk Agenda <-> Event
        Schema::create('event_agenda_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_agenda_id')->constrained('event_agendas')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
        });

        // 2. Modifikasi Tabel Event Programme
        Schema::table('event_programmes', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });

        // Buat tabel pivot untuk Programme <-> Event
        Schema::create('event_programme_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_programme_id')->constrained('event_programmes')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Rollback logic (opsional, agak rumit karena data hilang saat dropColumn)
        Schema::dropIfExists('event_agenda_event');
        Schema::dropIfExists('event_programme_event');
        
        Schema::table('event_agendas', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('cascade');
        });
        
        Schema::table('event_programmes', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('cascade');
        });
    }
};