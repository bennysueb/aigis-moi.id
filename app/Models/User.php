<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Registration;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Mail\ResetPasswordMail;
use Exception;



class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',      // <-- Tambahkan ini
        'nama_instansi',     // <-- Tambahkan ini
        'booth_number',      // <-- Tambahkan ini
        'tipe_instansi',     // <-- Tambahkan ini
        'phone_instansi',    // <-- Tambahkan ini
        'whatsapp',          // <-- Tambahkan ini
        'jabatan',           // <-- Tambahkan ini
        'alamat',            // <-- Tambahkan ini
        'tanda_tangan',      // <-- Tambahkan ini
        'description',
        'logo_path',
        'document_path',
        'website',
        'linkedin',
        'instagram',
        'facebook',
        'youtube_link',
        'document_link',
        'profile_data',
        'rfid_tag',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'profile_data' => 'array',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi untuk mengambil daftar peserta (attendees) yang telah di-scan oleh exhibitor.
     */
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'exhibitor_attendee', 'exhibitor_id', 'attendee_id')
            ->withTimestamps();
    }

    /**
     * Relasi untuk mengambil daftar exhibitor yang telah dikunjungi oleh peserta.
     */
    public function visitedExhibitors()
    {
        return $this->belongsToMany(User::class, 'exhibitor_attendee', 'attendee_id', 'exhibitor_id')
            ->withTimestamps();
    }

    public function favoritedExhibitors()
    {
        return $this->belongsToMany(User::class, 'favorite_exhibitors', 'user_id', 'exhibitor_id')
            ->withPivot('rating', 'is_loved') // <-- TAMBAHKAN INI
            ->withTimestamps();
    }

    public function sendPasswordResetNotification($token)
    {
        // Buat URL reset password
        $url = route('password.reset', ['token' => $token, 'email' => $this->getEmailForPasswordReset()]);

        try {
            // Ambil semua pengaturan yang relevan dari database
            $settings = Setting::all()->keyBy('key');

            // Ambil nama aplikasi, gunakan fallback jika tidak ada
            $appName = $settings['app_name']->value ?? config('app.name');

            // Buat array konfigurasi mailer kustom dari data di database
            $customConfig = [
                'transport'     => 'smtp',
                'host'          => $settings['mail_host']->value ?? config('mail.mailers.smtp.host'),
                'port'          => (int) ($settings['mail_port']->value ?? config('mail.mailers.smtp.port')),
                'encryption'    => $settings['mail_encryption']->value ?? config('mail.mailers.smtp.encryption'),
                'username'      => $settings['mail_username']->value ?? config('mail.mailers.smtp.username'),
                'password'      => $settings['mail_password']->value ?? config('mail.mailers.smtp.password'),
                'timeout'       => null,
                'auth_mode'     => null,
            ];

            // Terapkan konfigurasi ini ke mailer sementara bernama 'custom_smtp'
            Config::set('mail.mailers.custom_smtp', $customConfig);

            // Atur juga alamat 'from' secara dinamis
            $fromAddress = $settings['mail_from_address']->value ?? config('mail.from.address');
            $fromName = $settings['mail_from_name']->value ?? config('mail.from.name');
            Config::set('mail.from', ['address' => $fromAddress, 'name' => $fromName]);

            // Buat konten email
            $emailContent = "Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.\n\n" .
                "Klik link di bawah ini untuk mereset password Anda:\n" .
                $url . "\n\n" .
                "Link reset password ini akan kedaluwarsa dalam 60 menit.\n\n" .
                "Jika Anda tidak meminta reset password, abaikan email ini.\n\n" .
                "Terima kasih,\n" . $appName;

            // Perintahkan Laravel untuk menggunakan 'custom_smtp' yang baru kita buat
            Mail::mailer('custom_smtp')->raw($emailContent, function ($message) use ($appName) {
                $message->to($this->getEmailForPasswordReset())
                    ->subject($appName . ': Notifikasi Reset Password');
            });
        } catch (\Throwable $e) {
            // Jika terjadi error di lingkungan produksi, sebaiknya dicatat di log
            // agar tidak menampilkan error mentah kepada pengguna.
            // Log::error('Gagal mengirim email reset password: ' . $e->getMessage());
        }
    }

    // Relasi ke Tenant Profile (Jika user ini adalah penjual)
    public function tenant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    // Relasi ke Order Barang (Sebagai pembeli)
    public function productOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductOrder::class);
    }

    // Riwayat Transaksi (Gabungan tiket & barang)
    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
