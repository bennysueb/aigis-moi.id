<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update Events
        Schema::table('events', function (Blueprint $table) {
            // Cek dulu agar tidak error jika kolom sudah ada (opsional, untuk keamanan)
            if (!Schema::hasColumn('events', 'is_paid_event')) {
                $table->boolean('is_paid_event')->default(false)->after('slug');
            }
        });

        // Update Registrations
        Schema::table('registrations', function (Blueprint $table) {
            // 1. Tambahkan kolom referensi tiket
            if (!Schema::hasColumn('registrations', 'ticket_tier_id')) {
                $table->foreignId('ticket_tier_id')->nullable()->after('event_id');
            }

            // 2. Tambahkan status pembayaran
            // KITA UBAH POSISINYA: Ditaruh setelah 'ticket_tier_id' (yang baru dibuat) 
            // agar tidak error mencari kolom 'status'
            if (!Schema::hasColumn('registrations', 'payment_status')) {
                $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])
                    ->default('unpaid')
                    ->after('ticket_tier_id');
            }

            // 3. Tambahkan kolom harga final
            if (!Schema::hasColumn('registrations', 'total_price')) {
                $table->decimal('total_price', 15, 2)->default(0)->after('payment_status');
            }

            // 4. (Opsional) Kita buatkan kolom 'status' jika belum ada, 
            // karena sistem check-in biasanya butuh ini.
            if (!Schema::hasColumn('registrations', 'status')) {
                $table->enum('status', ['pending', 'confirmed', 'cancelled', 'attended'])
                    ->default('pending')
                    ->after('total_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_paid_event');
        });
        Schema::table('registrations', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $table->dropColumn(['ticket_tier_id', 'payment_status', 'total_price']);
            if (Schema::hasColumn('registrations', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
