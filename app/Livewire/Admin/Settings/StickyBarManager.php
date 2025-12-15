<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\Setting;
use App\Models\VideoGallery;
use App\Models\GalleryVideo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class StickyBarManager extends Component
{
    public array $links = [];
    public VideoGallery $gallery;
    public $galleryTitle;

    public $videos = [];

    // Kunci untuk link yang akan dikelola
    private $linkKeys = [
        'getting_there_url',
        'wikipedia_url',
        'instagram_url',
        'youtube_url',
        'whatsapp_url',
        'microsite_url'
    ];

    public function mount()
    {
        foreach ($this->linkKeys as $key) {
            $this->links[$key] = Setting::where('key', "stickybar.{$key}")->value('value') ?? '';
        }

        $this->gallery = VideoGallery::where('is_active', true)->firstOrNew();

        // DIUBAH: Isi properti baru dari model
        if (!$this->gallery->exists) {
            $this->galleryTitle = 'My Video Gallery'; // Default untuk galeri baru
        } else {
            $this->galleryTitle = $this->gallery->title; // Ambil dari galeri yang ada
        }

        $this->videos = $this->gallery->videos->toArray();
    }

    public function addVideo()
    {
        // Tambah baris video baru di form
        $this->videos[] = ['series_title' => '', 'youtube_embed_url' => ''];
    }

    public function removeVideo($index)
    {
        // Hapus video dari database jika sudah ada
        if (isset($this->videos[$index]['id'])) {
            GalleryVideo::find($this->videos[$index]['id'])->delete();
        }
        // Hapus dari array
        unset($this->videos[$index]);
        $this->videos = array_values($this->videos);
    }

    public function save()
    {
        // DIUBAH: Validasi properti yang baru
        $this->validate([
            'links.*' => 'nullable|url',
            'galleryTitle' => 'required|string|max:255', // Targetkan properti baru
            'videos.*.series_title' => 'required|string|max:255',
            'videos.*.youtube_embed_url' => 'required|url',
        ]);

        DB::transaction(function () {
            foreach ($this->links as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => "stickybar.{$key}"],
                    ['value' => $value]
                );
            }

            // DIUBAH: Update title model dari properti baru sebelum menyimpan
            $this->gallery->title = $this->galleryTitle;

            VideoGallery::where('id', '!=', $this->gallery->id)->update(['is_active' => false]);
            $this->gallery->is_active = true;
            $this->gallery->save();

            $currentVideoIds = [];
            foreach ($this->videos as $index => $videoData) {
                $video = GalleryVideo::updateOrCreate(
                    ['id' => $videoData['id'] ?? null],
                    [
                        'video_gallery_id' => $this->gallery->id,
                        'series_title' => $videoData['series_title'],
                        'youtube_embed_url' => $videoData['youtube_embed_url'],
                        'order' => $index,
                    ]
                );
                $currentVideoIds[] = $video->id;
            }

            $this->gallery->videos()->whereNotIn('id', $currentVideoIds)->delete();
        });

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.admin.settings.sticky-bar-manager');
    }
}
