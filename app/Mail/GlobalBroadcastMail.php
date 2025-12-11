<?php

namespace App\Mail;

use App\Models\EventEmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use stdClass;

class GlobalBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public EventEmailTemplate $template;
    public object $recipient;

    /**
     * Buat instance pesan baru.
     * Menerima template dan objek penerima (cukup nama & email).
     */
    public function __construct(EventEmailTemplate $template, object $recipient)
    {
        $this->template = $template;
        $this->recipient = $recipient;
    }

    public function envelope(): Envelope
    {
        $processedSubject = $this->processPlaceholders($this->template->subject);
        return new Envelope(subject: $processedSubject);
    }

    public function build()
    {
        // 1. Proses semua placeholder untuk subjek dan konten
        $processedSubject = $this->processPlaceholders($this->template->subject);
        $processedContent = $this->processPlaceholders($this->template->content);

        // 2. Dapatkan path absolut untuk logo dan banner
        $logoPath = config('settings.app_logo')
            ? storage_path('app/public/' . config('settings.app_logo'))
            : null;

        $bannerPath = $this->template->banner_path
            ? storage_path('app/public/' . $this->template->banner_path)
            : null;

        // 3. Bangun email dan kirim data ke view
        return $this->subject($processedSubject)
            ->view('emails.layouts.broadcast', [
                'subject' => $processedSubject,
                'content' => $processedContent,
                'logoPath' => file_exists($logoPath) ? $logoPath : null,
                'bannerPath' => file_exists($bannerPath) ? $bannerPath : null,
            ]);
    }

    private function processPlaceholders(string $text): string
    {
        $placeholders = [
            '{{ nama_peserta }}' => $this->recipient->name,
            '{{ app_name }}' => config('app.name'),
        ];

        $eventSpecificPlaceholders = [
            '[tanggal_acara]',
            '[info_acara_online]',
            '[info_lokasi_offline]',
            '[gambar_qr_code]',
            '[tombol_lihat_tiket]',
        ];

        $text = str_replace(array_keys($placeholders), array_values($placeholders), $text);
        $text = str_replace($eventSpecificPlaceholders, '', $text);

        return $text;
    }
    public function attachments(): array
    {
        return [];
    }
}
