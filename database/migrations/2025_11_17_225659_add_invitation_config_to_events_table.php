<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->longText('invitation_letter_body')->nullable();   // Isi Surat (HTML)
            $table->string('invitation_letter_header')->nullable();   // Path Gambar Kop Surat
            $table->json('invitation_files')->nullable();             // Array Path Dokumen Lampiran
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['invitation_letter_body', 'invitation_letter_header', 'invitation_files']);
        });
    }
};
