<?php

namespace App\Livewire\Admin\Banner;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithFileUploads; // Untuk menangani upload gambar

class Index extends Component
{
    use WithFileUploads;

    // Properti untuk Modal dan Form
    public bool $showModal = false;
    public bool $isEditMode = false;
    public ?Banner $currentBanner = null;

    // Properti untuk data binding form
    public string $headline = '';
    public ?string $subtitle;
    public string $features = ''; // Kita gunakan string, pisahkan dengan baris baru
    public ?string $button_text = '';
    public ?string $button_link = '';
    public bool $is_active = true;
    public ?string $gradient_from = '#000000';
    public ?string $gradient_to = '#ffffff';
    public float $opacity = 0.75;

    // Properti untuk gambar
    public $desktop_image;
    public $mobile_image;

    protected function rules()
    {
        return [
            'headline' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'features' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'desktop_image' => 'nullable|image|max:2048',
            'mobile_image' => 'nullable|image|max:2048',
            'gradient_from' => 'nullable|string|max:7',
            'gradient_to' => 'nullable|string|max:7',
            'opacity' => 'required|numeric|min:0|max:1',
        ];
    }

    public function render()
    {
        // Ambil semua banner dan urutkan
        $banners = Banner::orderBy('order')->get();
        return view('livewire.admin.banner.index', ['banners' => $banners])
            ->layout('layouts.app');
    }

    // Membuka modal untuk membuat banner baru
    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    // Membuka modal untuk mengedit banner yang ada
    public function edit(Banner $banner)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->currentBanner = $banner;

        // Isi form dengan data yang ada
        $this->headline = $banner->headline;
        $this->subtitle = $banner->subtitle;
        $this->features = implode("\n", $banner->features ?? []); // Ubah array jadi string
        $this->button_text = $banner->button_text;
        $this->button_link = $banner->button_link;
        $this->is_active = $banner->is_active;

        $this->gradient_from = $banner->gradient_from ?? '#000000';
        $this->gradient_to = $banner->gradient_to ?? '#ffffff';
        $this->opacity = $banner->opacity;

        $this->showModal = true;
    }

    // Menyimpan banner (baik baru maupun editan)
    public function save()
    {
        $this->validate();

        $data = [
            'headline' => $this->headline,
            'subtitle' => $this->subtitle,
            'features' => array_filter(explode("\n", $this->features)), // Ubah string jadi array
            'button_text' => $this->button_text,
            'button_link' => $this->button_link,
            'is_active' => $this->is_active,

            'gradient_from' => $this->gradient_from,
            'gradient_to' => $this->gradient_to,
            'opacity' => $this->opacity,
        ];

        if ($this->isEditMode) {
            // Update banner yang ada
            $this->currentBanner->update($data);
            $banner = $this->currentBanner;
            session()->flash('message', 'Banner updated successfully.');
        } else {
            // Buat banner baru
            $banner = Banner::create($data);
            session()->flash('message', 'Banner created successfully.');
        }

        // Proses upload gambar desktop
        if ($this->desktop_image) {
            // HAPUS GAMBAR LAMA SEBELUM MENAMBAH YANG BARU
            $banner->clearMediaCollection('desktop_image');

            $banner->addMedia($this->desktop_image->getRealPath())
                ->usingName($this->desktop_image->getClientOriginalName())
                ->toMediaCollection('desktop_image');
        }

        // Proses upload gambar mobile
        if ($this->mobile_image) {
            // HAPUS GAMBAR LAMA SEBELUM MENAMBAH YANG BARU
            $banner->clearMediaCollection('mobile_image');

            $banner->addMedia($this->mobile_image->getRealPath())
                ->usingName($this->mobile_image->getClientOriginalName())
                ->toMediaCollection('mobile_image');
        }

        $this->closeModal();
    }

    // Menghapus banner
    public function delete(Banner $banner)
    {
        $banner->delete();
        session()->flash('message', 'Banner deleted successfully.');
    }

    // Mengupdate urutan setelah drag-and-drop
    public function updateOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            Banner::where('id', $id)->update(['order' => $index + 1]);
        }
        $this->dispatch('notify', 'Banner order updated.');
    }

    // Menutup modal dan mereset form
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    // Fungsi helper untuk mereset semua properti form
    private function resetForm()
    {
        $this->reset(['isEditMode', 'currentBanner', 'headline', 'subtitle', 'features', 'button_text', 'button_link', 'is_active', 'desktop_image', 'mobile_image', 'gradient_from', 'gradient_to', 'opacity']);
    }
}
