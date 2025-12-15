<?php

namespace App\Livewire\Public;

use App\Models\SocialMediaType;
use App\Models\SocialWallItem;
use Livewire\Component;

class SocialWall extends Component
{
    public $filterType = null;
    public $items = [];
    public $perPage = 9;
    public $page = 1;
    public $hasMorePages;

    public function mount()
    {
        $this->loadItems();
    }

    public function loadItems()
    {
        $query = SocialWallItem::where('is_published', true)
            ->when($this->filterType, function ($query) {
                $query->where('social_media_type_id', $this->filterType);
            })
            ->latest();

        $paginator = $query->paginate($this->perPage, ['*'], 'page', $this->page);

        $this->items = array_merge($this->items, $paginator->items());

        $this->hasMorePages = $paginator->hasMorePages();

        $this->page++;

        $this->dispatch('items-loaded');
    }

    public function setFilter($typeId = null)
    {
        $this->filterType = $typeId;
        $this->resetPage();
    }

    public function resetPage()
    {
        $this->page = 1;
        $this->items = [];
        $this->loadItems();
    }

    public function render()
    {
        // Ambil hanya tipe yang memiliki item yang sudah dipublish
        $types = SocialMediaType::whereHas('items', function ($query) {
            $query->where('is_published', true);
        })->get();

        return view('livewire.public.social-wall', [
            'socialMediaTypes' => $types,
        ])->layout('layouts.app');
    }
}
