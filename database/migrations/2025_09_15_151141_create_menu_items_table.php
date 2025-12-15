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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->json('label'); // Untuk teks menu multi-bahasa (EN & ID)
            $table->string('link'); // URL tujuan
            $table->unsignedBigInteger('parent_id')->nullable(); // Untuk submenu/dropdown
            $table->integer('order')->default(0); // Untuk urutan menu
            $table->string('target')->default('_self'); // '_self' atau '_blank'
            $table->timestamps();

            // Menambahkan foreign key constraint untuk parent_id
            $table->foreign('parent_id')->references('id')->on('menu_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
