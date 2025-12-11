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
        Schema::table('events', function (Blueprint $table) {
            // Mengubah tipe kolom dari VARCHAR (string) menjadi TEXT
            $table->text('meeting_link')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Mengembalikan tipe kolom menjadi string(255) jika di-rollback
            $table->string('meeting_link')->nullable()->change();
        });
    }
};
