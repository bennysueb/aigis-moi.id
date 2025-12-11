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
            // Tambahkan kolom opacity setelah gradient_to
            // Tipe data decimal(3, 2) cocok untuk menyimpan nilai seperti 0.75
            $table->decimal('opacity', 3, 2)->default(0.75)->after('gradient_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('opacity');
        });
    }
};
