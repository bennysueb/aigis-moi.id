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
        Schema::create('inquiry_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_form_id')->constrained()->onDelete('cascade');
            $table->json('data'); // Untuk menyimpan data yang diisi pengguna (nama, email, dll)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_submissions');
    }
};
