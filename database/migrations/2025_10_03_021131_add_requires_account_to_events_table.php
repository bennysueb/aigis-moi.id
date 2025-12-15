<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Menambahkan kolom baru setelah kolom 'is_active'
            // Default 'false' berarti secara bawaan, event tidak wajib memiliki akun (pendaftaran tamu)
            $table->boolean('requires_account')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('requires_account');
        });
    }
};
