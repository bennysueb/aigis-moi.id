<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PendingBroadcast;
use App\Models\PendingEventBroadcast;
use App\Models\EventEmailTemplate;
use App\Models\Registration;
use App\Mail\GlobalBroadcastMail;
use App\Mail\DynamicBroadcastMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessPendingBroadcasts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcasts:process-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending global and event-specific broadcasts.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mulai memproses broadcast yang tertunda...');

        // 1. Proses Global Broadcasts
        $this->processGlobalBroadcasts();

        // 2. Proses Event Broadcasts
        $this->processEventBroadcasts();

        $this->info('Semua broadcast yang tertunda telah selesai diproses.');
        return 0;
    }

    /**
     * Process pending global broadcasts.
     */
    private function processGlobalBroadcasts()
    {
        // Ambil satu tugas global yang sedang 'pending' atau 'processing' untuk dilanjutkan
        $broadcast = PendingBroadcast::whereIn('status', ['pending', 'processing'])->first();

        if (!$broadcast) {
            $this->line('Tidak ada Global Broadcast yang perlu diproses.');
            return;
        }

        $this->line("Memproses Global Broadcast ID: {$broadcast->id}");
        
        if ($broadcast->status === 'pending') {
            $broadcast->update(['status' => 'processing']);
        }

        try {
            $template = $broadcast->template;
            if (!$template) {
                throw new \Exception("Template dengan ID {$broadcast->template_id} tidak ditemukan.");
            }

            // Ambil penerima secara bertahap (batch)
            $batchSize = 50; // Kirim 50 email per eksekusi
            $recipients = Registration::query()
                ->select('name', 'email')
                ->distinct('email')
                ->skip($broadcast->progress)
                ->take($batchSize)
                ->get();

            if ($recipients->isEmpty()) {
                $broadcast->update(['status' => 'completed']);
                $this->info("Global Broadcast ID: {$broadcast->id} selesai.");
                return;
            }

            foreach ($recipients as $recipient) {
                // Menggunakan queue untuk performa yang lebih baik
                Mail::to($recipient->email)->queue(new GlobalBroadcastMail($template, $recipient));
            }

            // Update progress
            $broadcast->increment('progress', $recipients->count());
            $this->info("{$recipients->count()} email untuk Global Broadcast ID: {$broadcast->id} telah dimasukkan ke antrean.");

        } catch (Throwable $e) {
            $broadcast->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            $this->error("Gagal memproses Global Broadcast ID: {$broadcast->id}. Error: " . $e->getMessage());
            Log::error("Global Broadcast Error (ID: {$broadcast->id}): " . $e->getMessage());
        }
    }

    /**
     * Process pending event-specific broadcasts.
     */
    private function processEventBroadcasts()
    {
        // Ambil satu tugas event broadcast yang sedang 'pending' atau 'processing'
        $broadcast = PendingEventBroadcast::whereIn('status', ['pending', 'processing'])->first();

        if (!$broadcast) {
            $this->line('Tidak ada Broadcast Acara yang perlu diproses.');
            return;
        }

        $this->line("Memproses Broadcast Acara ID: {$broadcast->id} untuk Acara ID: {$broadcast->event_id}");

        if ($broadcast->status === 'pending') {
            $broadcast->update(['status' => 'processing']);
        }
        
        try {
            $template = $broadcast->template;
            if (!$template) {
                throw new \Exception("Template dengan ID {$broadcast->template_id} tidak ditemukan.");
            }

            // Ambil pendaftar acara ini secara bertahap (batch)
            $batchSize = 50; // Kirim 50 email per eksekusi
            $registrations = Registration::where('event_id', $broadcast->event_id)
                ->skip($broadcast->progress)
                ->take($batchSize)
                ->get();

            if ($registrations->isEmpty()) {
                $broadcast->update(['status' => 'completed']);
                $this->info("Broadcast Acara ID: {$broadcast->id} selesai.");
                return;
            }
            
            foreach ($registrations as $registration) {
                 // Menggunakan queue untuk performa yang lebih baik
                Mail::to($registration->email)->queue(new DynamicBroadcastMail($template, $registration));
            }

            // Update progress
            $broadcast->increment('progress', $registrations->count());
            $this->info("{$registrations->count()} email untuk Broadcast Acara ID: {$broadcast->id} telah dimasukkan ke antrean.");

        } catch (Throwable $e) {
            $broadcast->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            $this->error("Gagal memproses Broadcast Acara ID: {$broadcast->id}. Error: " . $e->getMessage());
            Log::error("Event Broadcast Error (ID: {$broadcast->id}): " . $e->getMessage());
        }
    }
}