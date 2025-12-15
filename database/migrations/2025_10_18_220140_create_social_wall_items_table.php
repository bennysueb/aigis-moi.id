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
        Schema::create('social_wall_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_media_type_id')->constrained('social_media_types')->onDelete('cascade');
            $table->text('embed_code');
            $table->boolean('is_published')->default(false);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_wall_items');
    }
};
