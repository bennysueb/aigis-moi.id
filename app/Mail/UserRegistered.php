<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;



class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $qrCodeCid;
    // public $qrCode;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;

        // Membuat URL unik yang akan menjadi isi dari QR Code.
        // Kita gunakan UUID dari tabel 'registrations' yang sudah ada, 
        // dengan asumsi setiap user mendaftar ke suatu event utama saat register
        // ATAU kita gunakan UUID user jika logikanya umum. 
        // Untuk sekarang, kita gunakan rute yang sudah ada di aplikasi Anda.
        // **PENTING**: Kita asumsikan ada pendaftaran ke event utama saat user register
        // atau kita gunakan UUID user. Mari kita gunakan UUID user untuk QR code profil.

        // CATATAN: Karena belum ada rute untuk menampilkan profil user via UUID,
        // kita akan gunakan rute 'checkin.scan' sebagai placeholder.
        // Anda HARUS membuat rute baru nanti untuk QR code profil ini.
        // Contoh rute: /profile/{user:uuid}

        // **Untuk sementara, kita akan gunakan rute dari file `web.php` Anda**
        // Route::get('/check-in/{registration:uuid}', [RegistrationController::class, 'scanCheckIn'])->name('checkin.scan');
        // KITA AKAN GUNAKAN UUID USER, BUKAN REGISTRATION

        $url = route('scan.connect', ['uuid' => $this->user->uuid]); // Ganti 'registration' dengan nama parameter yang sesuai jika rute Anda berbeda.



    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome! Your Registration is Complete',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Membuat data gambar PNG
        $qrCodeData = QrCode::format('png')
            ->size(250)
            ->generate(route('scan.connect', ['uuid' => $this->user->uuid]));

        return new Content(
            view: 'emails.user-registered',
            with: [
                // Mengirim data gambar ke view, dan secara eksplisit mengubahnya menjadi string
                'qrCodeData' => (string) $qrCodeData,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
