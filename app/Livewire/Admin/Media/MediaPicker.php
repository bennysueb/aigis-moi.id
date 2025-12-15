<?php

namespace App\Livewire\Admin\Media;

use App\Models\Album;
use Livewire\Component;
use Livewire\Attributes\On;

class MediaPicker extends Component
{
    public $showModal = false;
    public $selectedAlbum;
    public $albums;

    #[On('open-media-modal')]
    public function openModal()
    {
        $this->showModal = true;
        $this->albums = Album::with('media')->latest()->get();
        if ($this->albums->isNotEmpty()) {
            $this->selectAlbum($this->albums->first()->id);
        }
    }

    public function selectAlbum($albumId)
    {
        $this->selectedAlbum = Album::findOrFail($albumId);
    }

    public function selectMedia($media)
    {
        $this->dispatch('media-selected', url: $media['url']);
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.admin.media.media-picker');
    }
}
