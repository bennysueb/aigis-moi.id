<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Pastikan tabel settings ada sebelum menjalankan query
        if (Schema::hasTable('settings')) {
            $settings = Setting::all();
            foreach ($settings as $setting) {
                Config::set('settings.' . $setting->key, $setting->value);
            }
        }

        // ==========================================================
        // --- BARU: Terapkan Konfigurasi Email dari Database ---
        // ==========================================================
        if (config('settings.mail_host')) {
            Config::set('mail.mailers.smtp.host', config('settings.mail_host'));
            Config::set('mail.mailers.smtp.port', config('settings.mail_port'));
            Config::set('mail.mailers.smtp.encryption', config('settings.mail_encryption'));
            Config::set('mail.mailers.smtp.username', config('settings.mail_username'));
            Config::set('mail.mailers.smtp.password', config('settings.mail_password'));
            Config::set('mail.from.address', config('settings.mail_from_address'));
            Config::set('mail.from.name', config('settings.mail_from_name'));
        }
    }
}
