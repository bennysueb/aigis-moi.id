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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->string('slug')->unique();
            $table->enum('type', ['article', 'video', 'audio']);
            $table->json('content')->nullable(); // Untuk tipe artikel
            $table->string('media_url')->nullable(); // Untuk link foto/video eksternal
            $table->string('source_name')->nullable(); // Untuk sumber berita artikel
            $table->string('source_url')->nullable();
            $table->string('source_favicon_url')->nullable();
            $table->timestamp('published_at')->nullable(); // Untuk jadwal posting
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
