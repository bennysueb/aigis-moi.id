<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('pending_broadcasts', function (Blueprint $table) {
            $table->integer('processed_count')->default(0)->after('status');
            $table->text('error_message')->nullable()->after('processed_count');
        });
    }
    public function down(): void {
        Schema::table('pending_broadcasts', function (Blueprint $table) {
            $table->dropColumn(['processed_count', 'error_message']);
        });
    }
};