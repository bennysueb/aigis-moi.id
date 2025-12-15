<?php

namespace App\Livewire\Admin\Collaborator;

use App\Models\Collaborator;
use App\Models\CollaboratorCategory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithFileUploads;

    // --- Data Source ---
    public $categories;
    public $selectedCategory = null;
    public $collaborators = [];

    // --- State Management ---
    public $showCategoryModal = false;
    public $showCollaboratorModal = false;
    public $isEditing = false;
    public $editingId = null;

    // --- Delete Confirmation State ---
    public $idToDelete = null;

    // --- Form Fields: Category ---
    public $cat_name;
    public $cat_type = 'partner'; // default

    // --- Form Fields: Collaborator ---
    public $col_name;
    public $col_url_link;
    public $col_logo_type = 'upload'; // upload or url
    public $col_logo_url_remote;
    public $col_logo_file; // Untuk file upload sementara

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'confirmedDeleteCategory',     // Listener saat user klik "Yes" di popup kategori
        'confirmedDeleteCollaborator'  // Listener saat user klik "Yes" di popup collaborator
    ];

    public function mount()
    {
        $this->loadCategories();
        // Pilih kategori pertama secara otomatis jika ada
        if ($this->categories->isNotEmpty()) {
            $this->selectCategory($this->categories->first()->id);
        }
    }

    public function loadCategories()
    {
        $this->categories = CollaboratorCategory::orderBy('sort_order')->get();
    }

    public function selectCategory($id)
    {
        $this->selectedCategory = CollaboratorCategory::find($id);
        $this->loadCollaborators();
    }

    public function loadCollaborators()
    {
        if ($this->selectedCategory) {
            $this->collaborators = $this->selectedCategory->collaborators()->orderBy('sort_order')->get();
        } else {
            $this->collaborators = [];
        }
    }

    // ==========================================
    // LOGIC: CATEGORY (Bagian Kiri)
    // ==========================================

    public function createCategory()
    {
        $this->reset(['cat_name', 'cat_type', 'isEditing', 'editingId']);
        $this->showCategoryModal = true;
    }

    public function editCategory($id)
    {
        $cat = CollaboratorCategory::find($id);
        $this->editingId = $id;
        $this->cat_name = $cat->name;
        $this->cat_type = $cat->type;
        $this->isEditing = true;
        $this->showCategoryModal = true;
    }

    public function saveCategory()
    {
        $this->validate([
            'cat_name' => 'required|string|max:255',
            'cat_type' => 'required|in:partner,sponsor',
        ]);

        if ($this->isEditing) {
            $cat = CollaboratorCategory::find($this->editingId);
            $cat->update([
                'name' => $this->cat_name,
                'type' => $this->cat_type,
            ]);
            $message = 'Category updated successfully!';
        } else {
            $maxOrder = CollaboratorCategory::max('sort_order') ?? 0;
            CollaboratorCategory::create([
                'name' => $this->cat_name,
                'type' => $this->cat_type,
                'sort_order' => $maxOrder + 1,
            ]);
            $message = 'Category created successfully!';
        }

        $this->showCategoryModal = false;
        $this->loadCategories();

        if ($this->isEditing && $this->selectedCategory && $this->selectedCategory->id == $this->editingId) {
            $this->selectCategory($this->editingId);
        }

        // Notifikasi Sukses
        $this->alert('success', $message);
    }

    public function confirmDeleteCategory($id)
    {
        $this->idToDelete = $id;
        $this->alert('warning', 'Are you sure?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'text' => 'All logos in this category will be deleted too!',
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmedDeleteCategory', // Panggil fungsi ini jika Yes
            'showCancelButton' => true,
            'confirmButtonText' => 'Yes, delete it!',
            'cancelButtonText' => 'No, cancel',
        ]);
    }

    public function confirmedDeleteCategory()
    {
        $cat = CollaboratorCategory::find($this->idToDelete);
        if ($cat) {
            $cat->delete();
            $this->loadCategories();
            $this->selectedCategory = null;
            $this->collaborators = [];

            $this->alert('success', 'Category deleted successfully!');
        }
    }

    public function updateCategoryOrder($list)
    {
        foreach ($list as $item) {
            CollaboratorCategory::where('id', $item['value'])->update(['sort_order' => $item['order']]);
        }
        $this->loadCategories();

        // Notifikasi Drag & Drop Sukses (Toast Kecil di Pojok)
        $this->alert('success', 'Category order updated!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    }

    // ==========================================
    // LOGIC: COLLABORATOR (Bagian Kanan)
    // ==========================================

    public function createCollaborator()
    {
        if (!$this->selectedCategory) return;
        $this->reset(['col_name', 'col_url_link', 'col_logo_type', 'col_logo_url_remote', 'col_logo_file', 'isEditing', 'editingId']);
        $this->showCollaboratorModal = true;
    }

    public function editCollaborator($id)
    {
        $col = Collaborator::find($id);
        $this->editingId = $id;
        $this->col_name = $col->name;
        $this->col_url_link = $col->url_link;
        $this->col_logo_type = $col->logo_type;
        $this->col_logo_url_remote = $col->logo_url_remote;
        $this->isEditing = true;
        $this->showCollaboratorModal = true;
    }

    public function saveCollaborator()
    {
        $this->validate([
            'col_name' => 'required|string|max:255',
            'col_url_link' => 'nullable|url',
            'col_logo_type' => 'required|in:upload,url',
            'col_logo_url_remote' => 'nullable|required_if:col_logo_type,url|url',
            'col_logo_file' => 'nullable|required_if:col_logo_type,upload|image|max:2048',
        ]);

        if ($this->isEditing) {
            $col = Collaborator::find($this->editingId);
            $col->update([
                'name' => $this->col_name,
                'url_link' => $this->col_url_link,
                'logo_type' => $this->col_logo_type,
                'logo_url_remote' => $this->col_logo_type == 'url' ? $this->col_logo_url_remote : null,
            ]);
            $message = 'Company updated successfully!';
        } else {
            $maxOrder = Collaborator::where('collaborator_category_id', $this->selectedCategory->id)->max('sort_order') ?? 0;
            $col = Collaborator::create([
                'collaborator_category_id' => $this->selectedCategory->id,
                'name' => $this->col_name,
                'url_link' => $this->col_url_link,
                'logo_type' => $this->col_logo_type,
                'logo_url_remote' => $this->col_logo_type == 'url' ? $this->col_logo_url_remote : null,
                'sort_order' => $maxOrder + 1,
            ]);
            $message = 'Company added successfully!';
        }

        if ($this->col_logo_type == 'upload' && $this->col_logo_file) {
            $col->clearMediaCollection('logo');
            $col->addMedia($this->col_logo_file)->toMediaCollection('logo');
        }

        $this->showCollaboratorModal = false;
        $this->loadCollaborators();

        // Notifikasi Sukses
        $this->alert('success', $message);
    }

    public function confirmDeleteCollaborator($id)
    {
        $this->idToDelete = $id;
        $this->alert('warning', 'Delete this logo?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'text' => 'This action cannot be undone.',
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmedDeleteCollaborator', // Panggil fungsi ini jika Yes
            'showCancelButton' => true,
            'confirmButtonText' => 'Yes, delete!',
            'cancelButtonText' => 'Cancel',
        ]);
    }

    public function confirmedDeleteCollaborator()
    {
        $col = Collaborator::find($this->idToDelete);
        if ($col) {
            $col->delete();
            $this->loadCollaborators();

            $this->alert('success', 'Logo deleted successfully!');
        }
    }

    public function updateCollaboratorOrder($list)
    {
        foreach ($list as $item) {
            Collaborator::where('id', $item['value'])->update(['sort_order' => $item['order']]);
        }
        $this->loadCollaborators();

        // Notifikasi Drag & Drop Sukses
        $this->alert('success', 'Logo order updated!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    }

    protected function alert($type = 'success', $message = '', $options = [])
    {
        $this->dispatch('alert', [
            'type' => $type,
            'message' => $message,
            'options' => $options
        ]);
    }

    public function render()
    {
        return view('livewire.admin.collaborator.index')
            ->layout('layouts.app'); // Sesuaikan dengan layout admin Anda
    }
}
