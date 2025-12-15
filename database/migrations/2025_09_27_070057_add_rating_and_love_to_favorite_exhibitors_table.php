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
        Schema::table('favorite_exhibitors', function (Blueprint $table) {
            // Rating dari 1 sampai 5, default 0 (belum ada rating)
            $table->tinyInteger('rating')->unsigned()->default(0)->after('exhibitor_id');
            // Status "love", default false
            $table->boolean('is_loved')->default(false)->after('rating');
        });
    }

    public function down(): void
    {
        Schema::table('favorite_exhibitors', function (Blueprint $table) {
            $table->dropColumn(['rating', 'is_loved']);
        });
    }
};
