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
        Schema::table('users', function (Blueprint $table) {
            // Kolom baru untuk Profil Instansi
            $table->string('phone_instansi')->nullable()->after('remember_token');

            // Kolom baru untuk Informasi Kontak
            $table->string('whatsapp')->nullable()->after('phone_instansi');

            // Kolom baru untuk Media & Materi Promosi
            $table->string('youtube_link')->nullable()->after('whatsapp');
            $table->string('document_path')->nullable()->after('youtube_link'); // Untuk menyimpan path file upload
            $table->string('document_link')->nullable()->after('document_path'); // Untuk link eksternal
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_instansi',
                'whatsapp',
                'youtube_link',
                'document_path',
                'document_link',
            ]);
        });
    }
};
