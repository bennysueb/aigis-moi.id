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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // Kolom JSON untuk judul multi-bahasa
            $table->string('slug')->unique(); // URL unik untuk halaman
            $table->json('content'); // Kolom JSON untuk konten multi-bahasa
            $table->string('status')->default('draft'); // Status: draft atau published
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
