<?php
// app/Http/View/Composers/FooterComposer.php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

class FooterComposer
{
    public function compose(View $view)
    {
        // Cache data selama 60 menit untuk performa
        $footerData = Cache::remember('footer_data', 3600, function () {
            return [
                'footerNavigation' => MenuItem::where('location', 'footer_nav')->orderBy('order')->get(),
                'footerLegal' => MenuItem::where('location', 'footer_legal')->orderBy('order')->get(),
            ];
        });

        // Kirim data ke view
        $view->with($footerData);
    }
}
