<?php

namespace App\Helpers;

use App\Models\Setting;
use App\Models\VideoGallery;
use Illuminate\Support\Facades\Cache;

class StickyBarHelper
{
    /**
     * Mengambil dan cache data untuk sticky bar.
     *
     * @return array
     */
    public static function getData(): array
    {
        // Cache data selama 60 menit
        return Cache::remember('sticky_bar_data', 60, function () {
            $linkKeys = [
                'getting_there_url',
                'wikipedia_url',
                'instagram_url',
                'youtube_url',
                'whatsapp_url',
                'microsite_url'
            ];
            $links = [];
            foreach ($linkKeys as $key) {
                $links[$key] = Setting::where('key', "stickybar.{$key}")->value('value');
            }

            $activeGallery = VideoGallery::where('is_active', true)
                ->with('videos') // Eager load videos
                ->first();

            return [
                'links' => $links,
                'gallery' => $activeGallery,
            ];
        });
    }
}
