<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Transaksi Pusat (Mencatat semua uang masuk via Midtrans)
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siapa yang bayar
            // Polymorphic relation: Bisa untuk Registration (Tiket) atau Order (Produk)
            $table->morphs('payable');
            $table->decimal('amount', 15, 2); // Total bayar
            $table->string('midtrans_transaction_id')->nullable(); // ID dari Midtrans
            $table->string('snap_token')->nullable(); // Token untuk popup
            $table->string('payment_type')->nullable(); // gopay, bank_transfer, dll
            $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'refunded'])->default('pending');
            $table->json('payload')->nullable(); // Simpan response mentah Midtrans buat debug
            $table->timestamps();
        });

        // 2. Tabel Penarikan Dana Tenant (Withdrawal)
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            // Kita akan buat tabel tenants sebentar lagi, jadi foreignId kita definisikan manual dulu atau nanti
            $table->unsignedBigInteger('tenant_id');
            $table->decimal('amount', 15, 2);
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_account_holder');
            $table->enum('status', ['requested', 'approved', 'rejected', 'transferred'])->default('requested');
            $table->string('admin_note')->nullable(); // Catatan admin jika reject
            $table->string('proof_of_transfer')->nullable(); // Bukti transfer admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('transactions');
    }
};
