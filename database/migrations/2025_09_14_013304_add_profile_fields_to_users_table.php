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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_instansi')->nullable()->after('password');
            $table->string('tipe_instansi')->nullable()->after('nama_instansi');
            $table->string('jabatan')->nullable()->after('tipe_instansi');
            $table->text('alamat')->nullable()->after('jabatan');
            $table->text('tanda_tangan')->nullable()->after('alamat'); // Untuk menyimpan data Base64
            $table->string('phone_number')->nullable()->after('email'); // Menambahkan phone_number jika belum ada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
