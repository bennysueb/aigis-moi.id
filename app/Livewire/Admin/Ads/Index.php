<?php

namespace App\Livewire\Admin\Ads;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    // Properti untuk Form
    public $ad_id;
    public $headline;
    public $url;
    public $position;
    public $is_active = false;
    public $image;
    public $existingImageUrl;

    // Properti untuk UI
    public $showModal = false;
    public $isEditMode = false;
    public $search = '';

    // Opsi Posisi Iklan
    public $positions = [
        'event_ad_left' => 'Event Page - Left Ad',
        'event_ad_right' => 'Event Page - Right Ad',
        'event_ad_bottom' => 'Event Page - Bottom Wide Ad',
    ];

    protected function rules()
    {
        return [
            'headline' => 'required|string|max:255',
            'url' => 'nullable|url',
            'position' => 'required|in:' . implode(',', array_keys($this->positions)),
            'is_active' => 'boolean',
            'image' => ($this->isEditMode && !$this->image) ? 'nullable' : 'required|image|max:2048', // Wajib jika membuat baru
        ];
    }

    public function render()
    {
        // Hanya tampilkan banner yang memiliki posisi (iklan)
        $ads = Banner::whereNotNull('position')
            ->where('headline', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.admin.ads.index', ['ads' => $ads])
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $ad = Banner::findOrFail($id);
        $this->ad_id = $ad->id;
        $this->headline = $ad->headline;
        $this->url = $ad->url;
        $this->position = $ad->position;
        $this->is_active = $ad->is_active;
        $this->existingImageUrl = $ad->hasMedia() ? $ad->getFirstMediaUrl('default', 'ad-tall') : null;

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'headline' => $this->headline,
            'url' => $this->url,
            'position' => $this->position,
            'is_active' => $this->is_active,
        ];

        // Buat atau update banner
        $banner = Banner::updateOrCreate(['id' => $this->ad_id], $data);

        // Proses upload gambar jika ada
        if ($this->image) {
            $banner->clearMediaCollection();
            $banner->addMedia($this->image->getRealPath())
                ->usingName($this->image->getClientOriginalName())
                ->toMediaCollection();
        }

        session()->flash('message', $this->isEditMode ? 'Ad updated successfully.' : 'Ad created successfully.');
        $this->closeModal();
    }

    public function delete($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();
        session()->flash('message', 'Ad deleted successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->ad_id = null;
        $this->headline = '';
        $this->url = '';
        $this->position = '';
        $this->is_active = false;
        $this->image = null;
        $this->existingImageUrl = null;
    }
}
