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
            // Menambahkan kolom visibility dengan default 'public'
            // Kita letakkan setelah kolom 'status' agar rapi
            if (!Schema::hasColumn('events', 'visibility')) {
                $table->string('visibility')->default('public')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'visibility')) {
                $table->dropColumn('visibility');
            }
        });
    }
};
