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
        Schema::create('inquiry_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Formulir Pendaftaran Sponsor"
            $table->string('slug')->unique();
            $table->json('fields'); // Untuk menyimpan definisi field (nama, tipe, label)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_forms');
    }
};
