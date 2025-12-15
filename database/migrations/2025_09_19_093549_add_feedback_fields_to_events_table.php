<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('feedback_form_id')->nullable()->after('inquiry_form_id')->constrained('feedback_forms')->onDelete('set null');
            $table->boolean('is_feedback_active')->default(false)->after('feedback_form_id');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['feedback_form_id']);
            $table->dropColumn(['feedback_form_id', 'is_feedback_active']);
        });
    }
};
