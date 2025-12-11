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
        Schema::table('welcome_sections', function (Blueprint $table) {
            $table->string('component')->nullable()->change();
            $table->foreignId('custom_section_id')->nullable()->after('component')->constrained('custom_sections')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('welcome_sections', function (Blueprint $table) {
            $table->dropForeign(['custom_section_id']);
            $table->dropColumn('custom_section_id');
            $table->string('component')->nullable(false)->change();
        });
    }
};
