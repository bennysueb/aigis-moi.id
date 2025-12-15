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
        Schema::table('registrations', function (Blueprint $table) {
            // Tambahkan kolom user_id setelah kolom event_id
            $table->foreignId('user_id')
                ->nullable() // Penting: Boleh kosong untuk pendaftar tamu (guest)
                ->constrained('users') // Membuat foreign key ke tabel users
                ->onDelete('cascade'); // Jika user dihapus, pendaftarannya juga terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
