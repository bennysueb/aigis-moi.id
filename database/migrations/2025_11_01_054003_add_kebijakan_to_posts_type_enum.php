<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- Pastikan ini ada

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menambahkan 'kebijakan' ke daftar ENUM
        DB::statement("ALTER TABLE posts MODIFY COLUMN type ENUM('article', 'video', 'audio', 'press_release', 'kebijakan') NOT NULL DEFAULT 'article'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengembalikan ke kondisi SEBELUM 'kebijakan' ditambahkan
        DB::statement("ALTER TABLE posts MODIFY COLUMN type ENUM('article', 'video', 'audio', 'press_release') NOT NULL DEFAULT 'article'");
    }
};
