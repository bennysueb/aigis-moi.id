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
        Schema::create('album_drive_photos', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel albums
            $table->foreignId('album_id')->constrained('albums')->onDelete('cascade');
            
            // Path atau ID file di Google Drive
            $table->string('file_id'); 
            
            // Nama file (untuk caption/alt text)
            $table->string('file_name')->nullable();
            
            // Tipe file (image/jpeg, dll)
            $table->string('mime_type')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('album_drive_photos');
    }
};
