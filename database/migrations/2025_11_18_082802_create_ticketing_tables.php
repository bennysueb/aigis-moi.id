<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Jenis Tiket (Pengganti harga statis di Event)
        Schema::create('ticket_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Early Bird, VIP, Regular
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('quota')->default(0); // Stok tiket
            $table->integer('max_per_user')->default(1); // Batas beli
            $table->dateTime('sales_start_at')->nullable();
            $table->dateTime('sales_end_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Voucher Master
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode Unik
            $table->foreignId('event_id')->nullable()->constrained()->onDelete('cascade'); // Null = Voucher Global
            // Kita tambahkan tenant_id nanti via alter table jika perlu voucher toko spesifik

            $table->enum('type', ['percentage', 'fixed_amount']);
            $table->decimal('amount', 15, 2); // Nilai diskon (misal 10% atau 50000)

            $table->integer('usage_limit')->default(0); // Kuota global
            $table->integer('usage_per_user')->default(1); // 1 user bisa pakai berapa kali

            $table->decimal('min_purchase_amount', 15, 2)->default(0);
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable(); // Expired date

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Log Penggunaan Voucher (History)
        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->uuid('transaction_id')->nullable(); // Link ke transaksi mana
            $table->decimal('discount_amount', 15, 2); // Berapa rupiah yang dihemat
            $table->timestamp('used_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('ticket_tiers');
    }
};
