<?php

namespace App\Livewire\Admin\News;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Str;

class Categories extends Component
{
    public $category_id;
    public $name_en, $name_id;
    public $slug;

    public $showModal = false;
    public $isEditMode = false;

    // --- PROPERTI BARU ---
    public $parent_id = null;
    public $allCategories = []; // Untuk mengisi dropdown
    // --------------------

    protected $rules = [
        'name_en' => 'required|string|max:255',
        // BARU: Validasi untuk parent_id (opsional tapi disarankan)
        'parent_id' => 'nullable|exists:categories,id',
    ];

    public function render()
    {
        // MODIFIKASI: Ambil kategori top-level (induk) dan eager-load sub-kategorinya (children)
        $categories = Category::whereNull('parent_id')->with('children')->latest()->get();

        // BARU: Ambil *semua* kategori untuk dropdown di modal
        $this->allCategories = Category::all();

        return view('livewire.admin.news.categories', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->category_id = $category->id;
        $this->name_en = $category->getTranslation('name', 'en');
        $this->name_id = $category->getTranslation('name', 'id');
        $this->slug = $category->slug;

        // MODIFIKASI: Muat parent_id saat mengedit
        $this->parent_id = $category->parent_id;

        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        // MODIFIKASI: Tambahkan parent_id ke data yang disimpan
        $data = [
            'name' => ['en' => $this->name_en, 'id' => $this->name_id],
            'slug' => Str::slug($this->name_en),
            'parent_id' => $this->parent_id ?: null // Simpan null jika kosong
        ];

        if ($this->isEditMode) {
            Category::find($this->category_id)->update($data);
            session()->flash('message', 'Category updated successfully.');
        } else {
            Category::create($data);
            session()->flash('message', 'Category created successfully.');
        }
        $this->closeModal();
    }

    public function delete($id)
    {
        // Peringatan: Ini akan menghapus kategori. 
        // Anda mungkin perlu menambahkan logika di sini untuk menangani apa yang terjadi pada sub-kategorinya.
        // Untuk saat ini, kita biarkan sesuai kode asli.
        Category::findOrFail($id)->delete();
        session()->flash('message', 'Category deleted successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->category_id = null;
        $this->name_en = '';
        $this->name_id = '';
        $this->slug = '';

        // MODIFIKASI: Reset parent_id juga
        $this->parent_id = null;
    }
}
