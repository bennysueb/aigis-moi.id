<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welcome_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama yang mudah dibaca, cth: "Upcoming Events"
            $table->string('component'); // Kunci unik, cth: "events"
            $table->integer('order')->default(0); // Untuk pengurutan
            $table->boolean('is_visible')->default(true); // Untuk toggle show/hide
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welcome_sections');
    }
};
