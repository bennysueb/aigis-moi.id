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
        Schema::table('banners', function (Blueprint $table) {
            // Tambahkan dua kolom baru setelah kolom 'button_link'
            $table->string('gradient_from')->nullable()->after('button_link');
            $table->string('gradient_to')->nullable()->after('gradient_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn(['gradient_from', 'gradient_to']);
        });
    }
};
