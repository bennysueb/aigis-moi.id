<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Event;

return new class extends Migration
{
    public function up(): void
    {
        // Langkah 1: Tambahkan kolom BARU dengan nama dan tipe yang benar
        Schema::table('events', function (Blueprint $table) {
            $table->json('youtube_recordings')->nullable()->after('status');
        });

        // Langkah 2: Pindahkan data dari kolom lama ke kolom baru
        // Kita cek dulu apakah kolom lama 'youtube_link' ada
        if (Schema::hasColumn('events', 'youtube_link')) {
            $eventsWithOldLink = DB::table('events')->whereNotNull('youtube_link')->where('youtube_link', '!=', '')->get();

            foreach ($eventsWithOldLink as $event) {
                // Buat format JSON yang baru
                $newJsonData = json_encode([
                    [
                        'title' => 'Event Recording', // Judul default
                        'link' => $event->youtube_link
                    ]
                ]);

                // Update baris dengan data JSON yang baru
                DB::table('events')->where('id', $event->id)->update(['youtube_recordings' => $newJsonData]);
            }

            // Langkah 3: Hapus kolom LAMA setelah data dipindahkan
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('youtube_link');
            });
        }
    }

    public function down(): void
    {
        // Logika untuk membatalkan migrasi (rollback)
        Schema::table('events', function (Blueprint $table) {
            $table->string('youtube_link')->nullable()->after('status');
        });
        // (Data dari JSON tidak bisa otomatis dikembalikan ke string, jadi kita hanya buat ulang kolom lama)
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('youtube_recordings');
        });
    }
};
