<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Kategori (Grouping)
        Schema::create('collaborator_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Platinum Sponsor", "Media Partner"
            $table->string('type')->default('partner'); // 'sponsor' atau 'partner' (untuk grouping tampilan)
            $table->integer('sort_order')->default(0); // Untuk drag & drop urutan kategori
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Kolaborator (Logo Perusahaan)
        Schema::create('collaborators', function (Blueprint $table) {
            $table->id();
            // Relasi ke kategori
            $table->foreignId('collaborator_category_id')
                  ->constrained('collaborator_categories')
                  ->onDelete('cascade');

            $table->string('name'); // Nama Perusahaan (muncul saat hover/tooltip)
            $table->string('url_link')->nullable(); // Link jika logo diklik
            
            // Logika Switch (Upload vs URL)
            $table->string('logo_type')->default('upload'); // 'upload' atau 'url'
            $table->string('logo_url_remote')->nullable(); // Jika pilih input URL gambar
            
            $table->integer('sort_order')->default(0); // Untuk drag & drop urutan logo
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collaborators');
        Schema::dropIfExists('collaborator_categories');
    }
};