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
            // Menambahkan kolom baru setelah kolom 'venue'
            $table->string('type')->default('offline')->after('venue');
            $table->string('platform')->nullable()->after('type');
            $table->string('meeting_link')->nullable()->after('platform');
            $table->json('meeting_info')->nullable()->after('meeting_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['type', 'platform', 'meeting_link', 'meeting_info']);
        });
    }
};
