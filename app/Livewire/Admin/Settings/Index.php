<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\Setting;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


use App\Models\User;      // Pastikan Model di-import
use App\Models\Event;     // Pastikan Model di-import
use App\Models\Banner;    // Pastikan Model di-import
use App\Models\News;;      // Pastikan Model di-import


class Index extends Component
{
    use WithFileUploads;

    // Properti untuk setiap pengaturan
    public $appName, $appLogo, $appFavicon, $metaTitle, $metaDescription, $metaKeywords, $contactEmail;

    public $mailHost, $mailPort, $mailUsername, $mailPassword, $mailEncryption, $mailFromAddress, $mailFromName;
    public $testEmailRecipient = '';

    // ProPERTI BARU UNTUK FOOTER (WhatsApp ditambahkan)
    public $footerLogo, $footerEmail, $footerPhone, $footerWhatsapp, $footerFacebookUrl, $footerInstagramUrl, $footerWikipediaUrl, $footerYoutubeUrl;

    // Properti untuk upload file baru
    public $newLogo;
    public $newFooterLogo; // Ganti nama agar tidak konflik dengan properti path
    public $newFavicon;

    // Daftar semua key yang kita kelola
    private $keys = [
        'app_name',
        'app_logo',
        'app_favicon',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'contact_email',
        'footer_logo',
        'footer_email',
        'footer_phone',
        'footer_whatsapp', // <-- PERUBAHAN DI SINI
        'footer_facebook_url',
        'footer_instagram_url',
        'footer_wikipedia_url',
        'footer_youtube_url',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        // Ambil semua pengaturan, gunakan key sebagai array key agar mudah diakses
        $settings = Setting::whereIn('key', $this->keys)->get()->keyBy('key');

        // Fungsi helper untuk mengambil nilai atau default
        $getValue = fn($key, $default = null) => $settings[$key]->value ?? $default;

        // Tetapkan nilai ke properti publik
        $this->appName = $getValue('app_name', config('app.name'));
        $this->appLogo = $getValue('app_logo');
        $this->appFavicon = $getValue('app_favicon');
        $this->metaTitle = $getValue('meta_title');
        $this->metaDescription = $getValue('meta_description');
        $this->metaKeywords = $getValue('meta_keywords');
        $this->contactEmail = $getValue('contact_email');
        $this->testEmailRecipient = auth()->user()->email;

        // Memuat pengaturan email
        $this->mailHost = $getValue('mail_host', config('mail.mailers.smtp.host'));
        $this->mailPort = $getValue('mail_port', config('mail.mailers.smtp.port'));
        $this->mailUsername = $getValue('mail_username', config('mail.mailers.smtp.username'));
        $this->mailPassword = $getValue('mail_password', config('mail.mailers.smtp.password'));
        $this->mailEncryption = $getValue('mail_encryption', config('mail.mailers.smtp.encryption'));
        $this->mailFromAddress = $getValue('mail_from_address', config('mail.from.address'));
        $this->mailFromName = $getValue('mail_from_name', config('mail.from.name'));

        // MEMUAT PENGATURAN FOOTER
        $this->footerLogo = $getValue('footer_logo');
        $this->footerEmail = $getValue('footer_email');
        $this->footerPhone = $getValue('footer_phone');
        $this->footerWhatsapp = $getValue('footer_whatsapp'); // <-- PERUBAHAN DI SINI
        $this->footerFacebookUrl = $getValue('footer_facebook_url');
        $this->footerInstagramUrl = $getValue('footer_instagram_url');
        $this->footerWikipediaUrl = $getValue('footer_wikipedia_url');
        $this->footerYoutubeUrl = $getValue('footer_youtube_url');
    }

    public function save()
    {
        // Validasi input
        $this->validate([
            'appName' => 'nullable|string|max:255',
            'newLogo' => 'nullable|image|max:1024', // max 1MB
            'newFooterLogo' => 'nullable|image|max:1024', // max 1MB
            'newFavicon' => 'nullable|image|mimes:ico,png|max:1024',
            'metaTitle' => 'nullable|string|max:255',
            'metaDescription' => 'nullable|string',
            'metaKeywords' => 'nullable|string',
            'contactEmail' => 'nullable|email',

            'mailHost' => 'nullable|string',
            'mailPort' => 'nullable|integer',
            'mailUsername' => 'nullable|string',
            'mailPassword' => 'nullable|string',
            'mailEncryption' => 'nullable|string|in:tls,ssl,', // Tambahkan string kosong untuk 'None'
            'mailFromAddress' => 'nullable|email',
            'mailFromName' => 'nullable|string',

            // VALIDASI BARU UNTUK FOOTER
            'footerEmail' => 'nullable|email',
            'footerPhone' => 'nullable|string|max:20',
            'footerWhatsapp' => 'nullable|string|max:20', // <-- PERUBAHAN DI SINI
            'footerFacebookUrl' => 'nullable|url',
            'footerInstagramUrl' => 'nullable|url',
            'footerWikipediaUrl' => 'nullable|url',
            'footerYoutubeUrl' => 'nullable|url',
        ]);

        // Fungsi helper untuk menyimpan
        $saveSetting = function ($key, $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        };

        // Simpan pengaturan teks
        $saveSetting('app_name', $this->appName);
        $saveSetting('meta_title', $this->metaTitle);
        $saveSetting('meta_description', $this->metaDescription);
        $saveSetting('meta_keywords', $this->metaKeywords);
        $saveSetting('contact_email', $this->contactEmail);

        // Menyimpan pengaturan email
        $saveSetting('mail_host', $this->mailHost);
        $saveSetting('mail_port', $this->mailPort);
        $saveSetting('mail_username', $this->mailUsername);
        $saveSetting('mail_password', $this->mailPassword);
        $saveSetting('mail_encryption', $this->mailEncryption);
        $saveSetting('mail_from_address', $this->mailFromAddress);
        $saveSetting('mail_from_name', $this->mailFromName);

        // MENYIMPAN PENGATURAN FOOTER
        $saveSetting('footer_email', $this->footerEmail);
        $saveSetting('footer_phone', $this->footerPhone);
        $saveSetting('footer_whatsapp', $this->footerWhatsapp); // <-- PERUBAHAN DI SINI
        $saveSetting('footer_facebook_url', $this->footerFacebookUrl);
        $saveSetting('footer_instagram_url', $this->footerInstagramUrl);
        $saveSetting('footer_wikipedia_url', $this->footerWikipediaUrl);
        $saveSetting('footer_youtube_url', $this->footerYoutubeUrl);

        // Proses upload logo jika ada file baru
        if ($this->newLogo) {
            $logoPath = $this->newLogo->store('logos', 'public');
            $saveSetting('app_logo', $logoPath);
        }

        if ($this->newFooterLogo) {
            $footerlogoPath = $this->newFooterLogo->store('logos', 'public');
            $saveSetting('footer_logo', $footerlogoPath);
        }

        // Proses upload favicon jika ada file baru
        if ($this->newFavicon) {
            $faviconPath = $this->newFavicon->store('favicons', 'public');
            $saveSetting('app_favicon', $faviconPath);
        }

        // Membersihkan cache setelah pengaturan diperbarui
        Artisan::call('cache:clear');

        // Beri pesan sukses
        session()->flash('message', 'Settings saved successfully.');

        // Muat ulang pengaturan untuk menampilkan path file yang baru
        $this->loadSettings();

        // Reset file input
        $this->newLogo = null;
        $this->newFooterLogo = null;
        $this->newFavicon = null;
    }

    public function sendTestEmail()
    {
        // 1. Validasi semua data yang diperlukan
        $this->validate([
            'testEmailRecipient' => 'required|email',
            'mailHost' => 'required|string',
            'mailPort' => 'required|integer',
            'mailUsername' => 'required|string',
            'mailPassword' => 'required|string',
            'mailFromAddress' => 'required|email',
            'mailFromName' => 'required|string',
        ]);

        try {
            // 2. Terapkan pengaturan dari form secara manual ke konfigurasi Laravel
            Config::set('mail.mailers.smtp.host', $this->mailHost);
            Config::set('mail.mailers.smtp.port', $this->mailPort);
            Config::set('mail.mailers.smtp.encryption', $this->mailEncryption);
            Config::set('mail.mailers.smtp.username', $this->mailUsername);
            Config::set('mail.mailers.smtp.password', $this->mailPassword);
            Config::set('mail.from.address', $this->mailFromAddress);
            Config::set('mail.from.name', $this->mailFromName);

            // 3. Ambil alamat email tujuan dari form
            $recipient = $this->testEmailRecipient;

            // 4. Kirim email
            Mail::raw('This is a test email from your application to verify the SMTP settings.', function ($message) use ($recipient) {
                $message->to($recipient)
                    ->subject('SMTP Configuration Test');
            });

            // 5. Jika berhasil, tampilkan notifikasi sukses
            session()->flash('mail_success', 'Test email sent successfully to ' . $recipient);
        } catch (\Exception $e) {
            // 6. Jika gagal, tangkap error dan tampilkan pesan
            session()->flash('mail_error', 'Failed to send email. Error: ' . $e->getMessage());
        }
    }

    public function clearCache()
    {
        try {
            Artisan::call('view:clear');

            // 1. Bersihkan Cache System
            Artisan::call('optimize:clear');

            // 2. Bersihkan File Upload Sementara (Livewire tmp)
            $this->cleanupOldUploads();

            // 3. [BARU] Hapus File "Yatim Piatu" (File ada di disk, tapi tidak ada di DB)
            $deletedFilesCount = $this->cleanupOrphanedFiles();

            // 4. Hapus Folder Kosong (Setelah file dihapus, folder jadi kosong, lalu kita hapus foldernya)
            $this->cleanupPublicTempFiles();
            $this->cleanupPublicEmptyFolders();

            session()->flash('cache_success', "Sistem di-refresh! Cache bersih. {$deletedFilesCount} file sampah dihapus & folder kosong dibersihkan.");
        } catch (\Exception $e) {
            session()->flash('cache_error', 'Gagal: ' . $e->getMessage());
        }
    }


    private function cleanupOrphanedFiles()
    {
        $disk = Storage::disk('public');
        $validFiles = [];

        // --- 1. WHITELIST DARI TABEL USERS (Logo & Dokumen) ---
        $users = \Illuminate\Support\Facades\DB::table('users')->get(['logo_path', 'document_path']);
        foreach ($users as $user) {
            if ($user->logo_path) $validFiles[] = $user->logo_path;
            if ($user->document_path) $validFiles[] = $user->document_path;
        }

        // --- 2. WHITELIST DARI TABEL TEMPLATE (Banner) ---
        $evtTemplates = \Illuminate\Support\Facades\DB::table('event_email_templates')->pluck('banner_path')->toArray();
        $bcTemplates = \Illuminate\Support\Facades\DB::table('broadcast_templates')->pluck('banner_path')->toArray();
        $validFiles = array_merge($validFiles, $evtTemplates, $bcTemplates);

        // --- 3. WHITELIST DARI TABEL POSTS (Media URL) ---
        $posts = \Illuminate\Support\Facades\DB::table('posts')->pluck('media_url')->toArray();
        $validFiles = array_merge($validFiles, $posts);

        // --- 4. [PENTING] WHITELIST DARI TABEL EVENTS (JSON: Personnel & Sponsors) ---
        // Kita ambil kolom JSON mentah
        $events = \Illuminate\Support\Facades\DB::table('events')->get(['personnel', 'sponsors']);

        foreach ($events as $event) {
            // A. Parse Personnel (Speakers & Moderators)
            // Struktur: {"speakers": [{"photo_url": "..."}], "moderators": [...]}
            if (!empty($event->personnel)) {
                $personnelData = json_decode($event->personnel, true);
                if (is_array($personnelData)) {
                    foreach ($personnelData as $group) { // Loop speakers, moderators
                        if (is_array($group)) {
                            foreach ($group as $person) {
                                if (!empty($person['photo_url'])) {
                                    $validFiles[] = $person['photo_url'];
                                }
                            }
                        }
                    }
                }
            }

            // B. Parse Sponsors
            // Struktur: {"platinum": [{"logo_url": "..."}], ...}
            if (!empty($event->sponsors)) {
                $sponsorsData = json_decode($event->sponsors, true);
                if (is_array($sponsorsData)) {
                    foreach ($sponsorsData as $tier) { // Loop platinum, gold, etc
                        if (is_array($tier)) {
                            foreach ($tier as $sponsor) {
                                if (!empty($sponsor['logo_url'])) {
                                    $validFiles[] = $sponsor['logo_url'];
                                }
                            }
                        }
                    }
                }
            }
        }

        // --- 5. WHITELIST DARI SETTINGS ---
        $settings = \Illuminate\Support\Facades\DB::table('settings')->pluck('value')->toArray();
        foreach ($settings as $val) {
            if (is_string($val) && (str_contains($val, '.') || str_contains($val, '/'))) {
                $validFiles[] = $val;
            }
        }

        // --- 6. NORMALISASI PATH ---
        // Ubah URL (misal: /storage/photos/abc.jpg) menjadi Path Disk (photos/abc.jpg)
        $validFiles = array_map(function ($path) {
            if (empty($path)) return '';
            // Hapus '/storage/' di depan jika ada (karena DB simpan URL, bukan path murni)
            $path = str_replace('/storage/', '', $path);
            // Hapus 'public/' jika ada
            $path = str_replace('public/', '', $path);
            return ltrim($path, '/');
        }, $validFiles);


        // --- 7. AMBIL ID MEDIA LIBRARY (SPATIE) ---
        // Ini melindungi Banner Event yang pakai Spatie
        $validMediaIds = \Illuminate\Support\Facades\DB::table('media')->pluck('id')->map(fn($id) => (string)$id)->toArray();


        // --- 8. EKSEKUSI PEMBERSIHAN ---
        $deletedCount = 0;
        $directories = $disk->directories();

        // Tahap A: Cek Folder Spatie
        foreach ($directories as $dir) {
            $dirName = basename($dir);
            if (is_numeric($dirName)) {
                if (!in_array($dirName, $validMediaIds)) {
                    try {
                        $disk->deleteDirectory($dir);
                        $deletedCount++;
                    } catch (\Exception $e) {
                    }
                }
            }
        }

        // Tahap B: Cek File Lepasan (Termasuk Photos & Logos dari Event)
        $allFiles = $disk->allFiles();
        foreach ($allFiles as $file) {
            if ($file === '.gitignore' || str_contains($file, 'default')) continue;

            // Skip file dalam folder Spatie yang valid
            $firstSegment = explode('/', $file)[0];
            if (is_numeric($firstSegment) && in_array($firstSegment, $validMediaIds)) {
                continue;
            }

            // Cek apakah file ada di whitelist yang sudah kita kumpulkan
            if (!in_array($file, $validFiles)) {
                try {
                    $disk->delete($file);
                    $deletedCount++;
                } catch (\Exception $e) {
                }
            }
        }

        return $deletedCount;
    }


    private function cleanupOldUploads()
    {
        // Kode sama seperti sebelumnya...
        $directory = storage_path('app/livewire-tmp');
        if (!File::isDirectory($directory)) return;
        $files = File::files($directory);
        $timestamp = now()->subHours(24)->getTimestamp();
        foreach ($files as $file) {
            if ($file->getFilename() === '.gitignore') continue;
            if ($file->getMTime() < $timestamp) {
                try {
                    File::delete($file->getPathname());
                } catch (\Exception $e) {
                }
            }
        }
    }

    private function cleanupPublicTempFiles()
    {
        // Pastikan folder ada
        $directory = storage_path('app/public');
        if (!File::isDirectory($directory)) return;

        $files = File::files($directory);

        // Ambil waktu 24 jam yang lalu
        $timestamp = now()->subHours(24)->getTimestamp();

        foreach ($files as $file) {
            // Jangan hapus .gitignore
            if ($file->getFilename() === '.gitignore') continue;

            // Hapus file jika umurnya lebih dari 24 jam
            if ($file->getMTime() < $timestamp) {
                try {
                    File::delete($file->getPathname());
                } catch (\Exception $e) {
                    // Silent fail (abaikan jika gagal hapus)
                }
            }
        }
    }

    private function cleanupPublicEmptyFolders()
    {
        $targetDir = storage_path('app/public');
        if (!File::isDirectory($targetDir)) return;
        $this->deleteEmptySubFolders($targetDir);
    }

    private function deleteEmptySubFolders($path)
    {
        if (!is_dir($path)) return;
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item != '.' && $item != '..') {
                $fullPath = $path . DIRECTORY_SEPARATOR . $item;
                if (is_dir($fullPath)) $this->deleteEmptySubFolders($fullPath);
            }
        }
        $remainingItems = array_diff(scandir($path), ['.', '..', '.gitignore']);
        if (count($remainingItems) === 0 && $path !== storage_path('app/public')) {
            try {
                rmdir($path);
            } catch (\Exception $e) {
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.index')
            ->layout('layouts.app'); // Sesuaikan dengan layout admin Anda
    }
}
