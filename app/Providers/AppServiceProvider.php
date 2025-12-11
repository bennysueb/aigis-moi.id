<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\MenuComposer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // LOGIKA PINTAR:
        // Hanya ubah path ke 'public_html' jika di Production (Hosting).
        // Jika di Local (Laptop), biarkan default (folder 'public').
        if ($this->app->environment('production')) {
            $this->app->usePublicPath(base_path('../public_html'));
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Force HTTPS hanya di Production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // 2. Load Settings (Kode kamu)
        try {
            // Cek koneksi DB dulu agar aman
            DB::connection()->getPdo();

            if (Schema::hasTable('settings')) {
                $settings = Cache::rememberForever('app_settings', function () {
                    return Setting::all()->keyBy('key')->map(function ($setting) {
                        return $setting->value;
                    });
                });

                Config::set('settings', $settings);

                if (isset($settings['app_name'])) {
                    Config::set('app.name', $settings['app_name']);
                }
            }
        } catch (\Exception $e) {
            // Error handling silent agar tidak crash saat migrasi awal
        }

        try {
            Storage::extend('google', function ($app, $config) {
                $options = [];

                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folderId'] ?? '/', $options);
                $driver  = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch (\Exception $e) {
            // Log error jika perlu, atau biarkan kosong agar tidak crash saat config belum lengkap
        }


        try {
            if (Schema::hasTable('settings')) {

                // 1. Ambil SEMUA settings dari cache, atau dari DB jika cache kosong
                $settings = Cache::rememberForever('app_settings', function () {

                    // 2. Ambil semua, 'keyBy' agar jadi array asosiatif (cth: 'app_name' => 'AIGIS MOI')
                    return Setting::all()->keyBy('key')->map(function ($setting) {
                        return $setting->value;
                    });
                });

                // 3. Muat semua settings ke 'config' Laravel
                // Sekarang kamu bisa panggil config('settings.app_name'), config('settings.footer_email'), dll.
                Config::set('settings', $settings);

                // 4. (Opsional) Override config 'app.name' bawaan Laravel
                // Kita ambil dari array $settings yang sudah di-load
                if (isset($settings['app_name'])) {
                    Config::set('app.name', $settings['app_name']);
                }
            }
        } catch (\Exception $e) {
            // Tangani error jika koneksi DB gagal, dll.
            \Log::error('Could not load settings from database: ' . $e->getMessage());
        }

        // Baris ini sudah benar dari kodemu, biarkan saja
        View::composer('livewire.layout.navigation', MenuComposer::class);

        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }
}
