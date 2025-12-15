<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->uuid('uuid')->unique(); // Token unik untuk link konfirmasi

            // Data Undangan (dari Excel)
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('company')->nullable(); // Instansi
            $table->string('category')->default('General'); // Kategori (VIP, Speaker, dll)

            // Status Pengiriman
            $table->boolean('is_sent_email')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            $table->boolean('is_sent_whatsapp')->default(false);
            $table->timestamp('whatsapp_sent_at')->nullable();

            // Status Respon
            // pending: Belum respon
            // confirmed: Bersedia Hadir
            // represented: Diwakilkan
            // declined: Tidak Hadir
            $table->enum('status', ['pending', 'confirmed', 'represented', 'declined'])->default('pending');
            $table->timestamp('responded_at')->nullable();

            // Jika Diwakilkan (Menyimpan data wakil sementara)
            $table->json('representative_data')->nullable();

            // Jika Tidak Hadir (Alasan penolakan)
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
