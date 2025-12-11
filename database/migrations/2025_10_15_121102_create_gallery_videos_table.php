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
        Schema::create('gallery_videos', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel video_galleries
            $table->foreignId('video_gallery_id')->constrained()->onDelete('cascade');
            $table->string('series_title'); // Judul per video (Series 1, dll)
            $table->text('youtube_embed_url'); // Link embed YouTube
            $table->integer('order')->default(0); // Untuk pengurutan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_videos');
    }
};
