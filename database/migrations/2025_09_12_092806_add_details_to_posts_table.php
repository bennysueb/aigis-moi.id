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
        Schema::table('posts', function (Blueprint $table) {
            // Kolom untuk Kategori & Penulis
            $table->foreignId('user_id')->nullable()->after('slug')->constrained()->onDelete('set null');
            $table->foreignId('category_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            $table->foreignId('subcategory_id')->nullable()->after('category_id')->constrained('categories')->onDelete('set null');

            // Kolom untuk Opsi Visibilitas & Penandaan (Flags)
            $table->json('visibility_options')->nullable()->after('source_favicon_url');

            // Kolom untuk SEO
            $table->json('seo_meta')->nullable()->after('visibility_options');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            //
        });
    }
};
