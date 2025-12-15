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
            // Tambahkan baris ini
            $table->timestamp('feedback_email_sent_at')->nullable()->after('checked_in_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Tambahkan baris ini untuk berjaga-jaga jika perlu rollback
            $table->dropColumn('feedback_email_sent_at');
        });
    }
};
