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
        Schema::table('event_email_templates', function (Blueprint $table) {
            // Mengubah kolom event_id agar bisa bernilai NULL (opsional)
            $table->foreignId('event_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_email_templates', function (Blueprint $table) {
            // Mengembalikan kolom event_id menjadi tidak bisa NULL jika migrasi di-rollback
            $table->foreignId('event_id')->nullable(false)->change();
        });
    }
};
