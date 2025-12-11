<?php

namespace App\Mail;

use App\Models\EventEmailTemplate;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Mail\Mailables\Attachment;

class DynamicBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public EventEmailTemplate $template;
    public Registration $registration;
    protected ?string $qrCodeTempPath = null;

    /**
     * Create a new message instance.
     */
    public function __construct(EventEmailTemplate $template, Registration $registration)
    {
        $this->template = $template;
        $this->registration = $registration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Proses placeholder sederhana untuk subjek
        $processedSubject = $this->processSimplePlaceholders($this->template->subject);

        return new Envelope(
            subject: $processedSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        // 1. Proses semua placeholder untuk subjek dan konten
        $processedSubject = $this->processSimplePlaceholders($this->template->subject);
        $processedContent = $this->processSimplePlaceholders($this->template->content);
        $processedContent = $this->processConditionalPlaceholders($processedContent);

        // 2. Dapatkan path absolut (alamat lengkap di server) untuk logo dan banner
        $logoPath = config('settings.app_logo')
            ? storage_path('app/public/' . config('settings.app_logo'))
            : null;

        $bannerPath = $this->template->banner_path
            ? storage_path('app/public/' . $this->template->banner_path)
            : null;

        // 3. Bangun email, atur subjek, dan kirim data (termasuk path gambar) ke view
        return $this->subject($processedSubject)
            ->view('emails.layouts.broadcast', [
                'subject' => $processedSubject,
                'content' => $processedContent,
                'logoPath' => file_exists($logoPath) ? $logoPath : null,
                'bannerPath' => file_exists($bannerPath) ? $bannerPath : null,
            ]);
    }

    // Method __destruct akan otomatis dipanggil setelah objek tidak lagi digunakan (email terkirim)
    public function __destruct()
    {
        // Hapus file QR code sementara jika ada
        if ($this->qrCodeTempPath && file_exists($this->qrCodeTempPath)) {
            unlink($this->qrCodeTempPath);
        }
    }


    /**
     * Proses placeholder sederhana seperti {{ nama_peserta }}.
     */
    private function processSimplePlaceholders(string $text): string
    {
        $placeholders = [
            '{{ nama_peserta }}' => $this->registration->name,
            '{{ nama_acara }}' => $this->registration->event->name,
            '{{ tipe_kehadiran }}' => ucfirst($this->registration->attendance_type ?? $this->registration->event->type),
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $text);
    }

    /**
     * Proses placeholder blok kondisional seperti [gambar_qr_code].
     */
    private function processConditionalPlaceholders(string $content): string
    {
        $attendanceType = $this->registration->attendance_type ?? $this->registration->event->type;
        $event = $this->registration->event;

        // Siapkan HTML untuk blok QR Code dengan narasi yang benar
        // PERUBAHAN: Menggunakan cid:qrcode.png yang berasal dari method attachments()
        $qrCodeHtml = '<div style="text-align: center; padding: 20px 0 30px 0;">' .
            '<p style="margin: 0 0 15px 0; color: #333333;">To speed up the check-in process at the venue, please have the QR Code below ready for our staff to scan. You can also access it via the button below.</p>' .
            '<img src="cid:qrcode.png" alt="QR Code Ticket" style="display: inline-block;">' .
            '</div>';

        $replacements = [
            '[tanggal_acara]' => view('emails.partials._date-format', ['event' => $event])->render(),
            '[info_acara_online]' => $attendanceType !== 'offline' ? view('emails.partials._online-info', ['event' => $event])->render() : '',
            '[info_lokasi_offline]' => $attendanceType === 'offline' ? view('emails.partials._offline-info', ['event' => $event])->render() : '',
            '[tombol_lihat_tiket]' => $attendanceType === 'offline' ? view('emails.partials._ticket-button', ['registration' => $this->registration])->render() : '',
            '[gambar_qr_code]' => $attendanceType === 'offline' ? $qrCodeHtml : '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }


    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];
        $attendanceType = $this->registration->attendance_type ?? $this->registration->event->type;

        if ($attendanceType === 'offline' && str_contains($this->template->content, '[gambar_qr_code]')) {

            $directory = storage_path('app/public/qrcodes');
            if (!file_exists($directory)) {
                mkdir($directory, 0775, true);
            }

            $this->qrCodeTempPath = storage_path('app/public/qrcodes/' . Str::random(40) . '.png');
            QrCode::format('png')->size(250)->margin(1)->generate(
                route('checkin.scan', $this->registration->uuid),
                $this->qrCodeTempPath
            );

            // Lampiran ini akan di-embed dengan Content-ID (cid) 'qrcode.png'
            $attachments[] = Attachment::fromPath($this->qrCodeTempPath)
                ->as('qrcode.png')
                ->withMime('image/png');
        }

        return $attachments;
    }
}
