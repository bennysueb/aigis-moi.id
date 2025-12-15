<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\FooterComposer;

class FooterComposerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Memberitahu Laravel untuk menjalankan FooterComposer setiap kali view ini dipanggil
        View::composer(['layouts.guest', 'layouts.app'], FooterComposer::class);
    }
}
