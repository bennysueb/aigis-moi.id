<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Profil Tenant (Toko)
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Owner
            $table->string('name'); // Nama Toko
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();

            // Info Bank Tenant (untuk admin transfer manual)
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_holder')->nullable();

            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->decimal('balance', 15, 2)->default(0); // Dompet Tenant
            $table->timestamps();
        });

        // 2. Produk
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('stock')->default(0); // Sesuai request: Manajemen Stok
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Order (Keranjang Belanja)
        Schema::create('product_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained(); // Pembeli
            $table->foreignId('tenant_id')->constrained(); // Penjual

            // Biaya
            $table->decimal('total_amount', 15, 2); // Harga barang total
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('admin_fee', 15, 2)->default(0); // Potongan admin (jika ada)
            $table->decimal('final_amount', 15, 2); // Yang harus dibayar user

            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->string('shipping_address')->nullable(); // Opsional jika barang fisik
            $table->string('tracking_number')->nullable(); // No Resi
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });

        // 4. Item Order Detail
        Schema::create('product_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('product_order_id')->constrained('product_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->decimal('price_at_purchase', 15, 2); // Harga saat beli (penting untuk laporan)
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_order_items');
        Schema::dropIfExists('product_orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('tenants');
    }
};
