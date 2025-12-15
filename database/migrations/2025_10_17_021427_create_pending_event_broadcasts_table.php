<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_event_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->constrained('event_email_templates')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->integer('progress')->default(0);
            $table->integer('total_recipients')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_event_broadcasts');
    }
};
