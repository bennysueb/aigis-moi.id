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
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('confirmation_template_id')
                ->nullable()
                ->after('feedback_form_id') // Meletakkan kolom setelah feedback_form_id
                ->constrained('event_email_templates') // Terhubung ke tabel event_email_templates
                ->onDelete('set null'); // Jika template dihapus, kolom ini akan menjadi NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Hapus foreign key constraint dulu sebelum menghapus kolom
            $table->dropForeign(['confirmation_template_id']);
            $table->dropColumn('confirmation_template_id');
        });
    }
};
