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
        Schema::create('favorite_exhibitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pengguna yang memberi favorit
            $table->foreignId('exhibitor_id')->constrained('users')->onDelete('cascade'); // Exhibitor yang difavoritkan
            $table->timestamps();

            $table->unique(['user_id', 'exhibitor_id']); // Mencegah duplikasi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_exhibitors');
    }
};
