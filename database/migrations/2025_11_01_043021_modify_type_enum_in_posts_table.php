<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- PASTIKAN ANDA MENAMBAHKAN INI

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kita gunakan SQL mentah untuk bypass Doctrine
        DB::statement("ALTER TABLE posts MODIFY COLUMN type ENUM('article', 'video', 'audio', 'press_release') NOT NULL DEFAULT 'article'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Perintah untuk rollback
        DB::statement("ALTER TABLE posts MODIFY COLUMN type ENUM('article', 'video', 'audio') NOT NULL DEFAULT 'article'");
    }
};
