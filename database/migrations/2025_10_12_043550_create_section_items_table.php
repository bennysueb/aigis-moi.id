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
        Schema::create('section_items', function (Blueprint $table) {
            $table->id();

            // Menghubungkan ke tabel welcome_sections
            $table->foreignId('welcome_section_id')->constrained()->onDelete('cascade');

            // Kolom untuk hubungan polimorfik
            $table->unsignedBigInteger('item_id');
            $table->string('item_type');

            // Kolom untuk pengurutan
            $table->integer('order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_items');
    }
};
