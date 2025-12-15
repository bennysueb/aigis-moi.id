<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegistrationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Buat instance pesan baru.
     */
    public function __construct(
        public Registration $registration
    ) {}

    /**
     * Dapatkan amplop pesan.
     */
    public function envelope(): Envelope
    {
        // Menentukan tipe kehadiran final
        $attendanceType = $this->registration->event->type === 'hybrid'
            ? $this->registration->attendance_type
            : $this->registration->event->type;

        $subject = $attendanceType === 'offline'
            ? 'Your ticket for: ' . $this->registration->event->name
            : 'Registration Confirmation for: ' . $this->registration->event->name;

        return new Envelope(subject: $subject);
    }

    /**
     * Dapatkan definisi konten pesan.
     */
    public function content(): Content
    {
        $qrCodeImage = null;

        // Menentukan tipe kehadiran final (ini sudah benar)
        $attendanceType = $this->registration->event->type === 'hybrid'
            ? $this->registration->attendance_type
            : $this->registration->event->type;

        // --- PERBAIKANNYA DI SINI ---
        // Gunakan $attendanceType untuk mengecek, bukan $this->registration->event->type
        if ($attendanceType === 'offline') {
            $tempFilePath = tempnam(sys_get_temp_dir(), 'qrcode') . '.png';

            QrCode::format('png')
                ->size(250)
                ->margin(1)
                ->generate(route('checkin.scan', $this->registration->uuid), $tempFilePath);

            $qrCodeImage = file_get_contents($tempFilePath);

            unlink($tempFilePath);
        }

        return new Content(
            view: 'emails.registration-confirmation',
            with: [
                'qrCodeImage' => $qrCodeImage,
            ],
        );
    }

    /**
     * Dapatkan lampiran untuk pesan.
     * Kita kosongkan karena QR code sudah ada di body.
     */
    public function attachments(): array
    {
        return [];
    }
}
