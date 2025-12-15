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
        // Membuat tabel pivot untuk relasi Many-to-Many
        // antara 'posts' dan 'categories'
        Schema::create('category_post', function (Blueprint $table) {
            // Foreign key untuk 'posts'
            $table->foreignId('post_id')
                ->constrained('posts')
                ->onDelete('cascade'); // Jika post dihapus, relasi ini juga hilang

            // Foreign key untuk 'categories'
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('cascade'); // Jika kategori dihapus, relasi ini juga hilang

            // Menjadikan kedua kolom sebagai primary key
            // Ini juga mencegah duplikat (satu post tidak bisa
            // di-link ke kategori yang sama lebih dari sekali)
            $table->primary(['post_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_post');
    }
};
