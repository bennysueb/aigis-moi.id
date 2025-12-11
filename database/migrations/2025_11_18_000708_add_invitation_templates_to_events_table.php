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
        Schema::table('events', function (Blueprint $table) {
            $table->text('invitation_wa_template')->nullable();      // Template WA
            $table->string('invitation_email_subject')->nullable();  // Subject Email
            $table->text('invitation_email_body')->nullable();       // Body Email
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['invitation_wa_template', 'invitation_email_subject', 'invitation_email_body']);
        });
    }
};
