<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Ubah kolom payable_id menjadi string dengan panjang 36 (cukup untuk UUID)
            // Ini aman karena ID Integer (misal: 1, 2, 100) juga bisa disimpan sebagai string
            $table->string('payable_id', 36)->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Kembalikan ke integer jika di-rollback (Hati-hati, ini akan error jika ada data UUID)
            // Kita gunakan unsignedBigInteger agar sesuai standar morphs awal
            // $table->unsignedBigInteger('payable_id')->change(); 

            // Catatan: Biasanya rollback perubahan tipe data seperti ini sulit jika datanya sudah tercampur.
        });
    }
};
