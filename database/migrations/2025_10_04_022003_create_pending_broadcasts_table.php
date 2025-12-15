<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('event_email_templates')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, processing, sent
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_broadcasts');
    }
};