<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ExhibitorRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;

        // Membuat URL unik untuk QR Code Booth Exhibitor.
        // URL ini bisa mengarah ke halaman profil publik exhibitor nantinya.
        // Untuk sekarang, kita buat placeholder URL.
        $url = route('scan.connect', ['uuid' => $this->user->uuid]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome Exhibitor! Your Registration is Complete',
        );
    }

    public function content(): Content
    {
        // Membuat data gambar PNG
        $qrCodeData = QrCode::format('png')
            ->size(250)
            ->generate(route('scan.connect', ['uuid' => $this->user->uuid]));

        return new Content(
            view: 'emails.exhibitor-registered',
            with: [
                // Mengirim data gambar ke view, dan secara eksplisit mengubahnya menjadi string
                'qrCodeData' => (string) $qrCodeData,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
