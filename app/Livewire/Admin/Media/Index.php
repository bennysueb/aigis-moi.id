<?php

namespace App\Livewire\Admin\Media;

use App\Models\Album;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\AlbumDrivePhoto;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithFileUploads;
    
    public $itemToDeleteId;
    public $itemToDeleteType;

    public $selectedAlbum;
    public $showFilePicker = false;
    public $files = [];
    public $showAlbumModal = false;
    public $showUploadModal = false;
    public $newAlbumName = '';

    // === TAMBAHAN UNTUK EDIT ===
    public $showEditModal = false;
    public $editingAlbum; // Ini akan menampung model Album yang diedit
    public $editingAlbumName = ''; // Ini untuk input form edit
    // === AKHIR TAMBAHAN ===

    public function render()
    {
        $albums = Album::with('media')->latest()->get();
        return view('livewire.admin.media.index', ['albums' => $albums])
            ->layout('layouts.app');
    }

    public function selectAlbum($albumId)
    {
        $this->selectedAlbum = Album::findOrFail($albumId);
    }
    
    public function backToAlbums()
    {
        $this->selectedAlbum = null;
        $this->files = [];
        $this->showFilePicker = false;
    }

    public function createAlbum()
    {
        $this->validate(['newAlbumName' => 'required|string|max:255']);

        // PENYEMPURNAAN SLUG:
        // Karena model Album.php sudah 'use HasSlug',
        // baris ini secara otomatis membuat 'name' DAN 'slug'
        Album::create(['name' => $this->newAlbumName]);

        $this->showAlbumModal = false;
        $this->newAlbumName = '';
        session()->flash('message', 'Album created successfully.');
    }

    // === FUNGSI BARU UNTUK MEMBUKA MODAL EDIT ===
    public function openEditModal($albumId)
    {
        $album = Album::findOrFail($albumId);
        $this->editingAlbum = $album;
        $this->editingAlbumName = $album->name;
        $this->showEditModal = true;
    }

    // === FUNGSI BARU UNTUK MENYIMPAN PERUBAHAN ===
    public function updateAlbum()
    {
        // Pastikan ada album yang diedit
        if (!$this->editingAlbum) {
            return;
        }

        // Validasi nama baru
        $this->validate(['editingAlbumName' => 'required|string|max:255']);

        // PENYEMPURNAAN SLUG:
        // Paket 'sluggable' juga akan otomatis
        // memperbarui 'slug' jika 'name' berubah.
        $this->editingAlbum->update([
            'name' => $this->editingAlbumName
        ]);

        // Reset dan tutup modal
        $this->showEditModal = false;
        $this->editingAlbum = null;
        $this->editingAlbumName = '';

        // Refresh album yang dipilih jika itu yang baru saja diedit
        if ($this->selectedAlbum && $this->selectedAlbum->id == $this->editingAlbum->id) {
            $this->selectAlbum($this->editingAlbum->id);
        }

        session()->flash('message', 'Album updated successfully.');
    }
    
    public function confirmDeleteAlbum($id)
    {
        $this->itemToDeleteId = $id;
        // Kirim sinyal ke JS untuk tampilkan popup
        $this->dispatch('show-delete-confirmation', context: 'album');
    }

    // --- LOGIC HAPUS FOTO (PEMICU) ---
    public function confirmDeletePhoto($id, $type)
    {
        $this->itemToDeleteId = $id;
        $this->itemToDeleteType = $type;
        $this->dispatch('show-delete-confirmation', context: 'photo');
    }

    #[On('perform-delete-album')]
    public function deleteAlbum()
    {
        if (!$this->itemToDeleteId) return;

        $album = Album::findOrFail($this->itemToDeleteId);
        $album->clearMediaCollection(); 
        $album->delete();

        if ($this->selectedAlbum && $this->selectedAlbum->id == $this->itemToDeleteId) {
            $this->selectedAlbum = null;
        }

        $this->dispatch('swal:success', message: 'Album berhasil dihapus.');
        $this->itemToDeleteId = null;
    }

    // --- EKSEKUSI HAPUS FOTO (DIPANGGIL SETELAH KONFIRMASI) ---
    #[On('perform-delete-photo')]
    public function deletePhoto()
    {
        if (!$this->itemToDeleteId) return;

        if ($this->itemToDeleteType === 'local') {
            Media::findOrFail($this->itemToDeleteId)->delete();
        } else {
            AlbumDrivePhoto::findOrFail($this->itemToDeleteId)->delete();
        }
        
        if ($this->selectedAlbum) {
            $this->selectAlbum($this->selectedAlbum->id);
        }
        
        $this->dispatch('swal:success', message: 'Foto berhasil dihapus.');
        $this->itemToDeleteId = null;
        $this->itemToDeleteType = null;
    }

    public function uploadFiles()
    {
        $this->validate(['files.*' => 'image|max:2048']); // Maks 2MB per file

        foreach ($this->files as $file) {
            $this->selectedAlbum->addMedia($file)->toMediaCollection();
        }

        $this->showUploadModal = false;
        $this->files = [];
        $this->selectAlbum($this->selectedAlbum->id); // Refresh media list
        session()->flash('message', 'Files uploaded successfully.');
    }

    public function openDrivePicker()
    {
        // Pastikan album sudah dipilih
        if (!$this->selectedAlbum) {
            return;
        }
        
        $this->showFilePicker = true;
        
        // Perintah ke AlpineJS untuk membuka modal
        $this->dispatch('open-modal', 'gallery-file-picker'); 
    }

    // --- FUNGSI BARU: Simpan Foto dari Drive ---
    #[On('fileSelected')]
    public function handleDriveSelection($data)
    {
        if ($this->selectedAlbum) {
            // Simpan referensi ke database (Tabel Baru)
            // Kita TIDAK mendownload file, hanya simpan ID/Path-nya
            AlbumDrivePhoto::create([
                'album_id' => $this->selectedAlbum->id,
                'file_id' => $data['path'], // Path di Google Drive
                'file_name' => basename($data['path']),
                'mime_type' => 'image/jpeg', // Default (nanti dideteksi ulang saat stream)
            ]);

            $this->dispatch('swal:success', message: 'Foto dari Drive berhasil ditambahkan!');
            
            // Refresh tampilan album
            $this->selectAlbum($this->selectedAlbum->id);
        }
        
        // Tutup Modal
        $this->showFilePicker = false;
        $this->dispatch('close-modal', 'gallery-file-picker');
    }

}
