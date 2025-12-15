<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Kolom untuk menyimpan pilihan 'offline' atau 'online' untuk event hybrid
            $table->string('attendance_type')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('attendance_type');
        });
    }
};
