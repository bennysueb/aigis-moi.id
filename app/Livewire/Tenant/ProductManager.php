<?php

namespace App\Livewire\Tenant;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // Wajib untuk upload gambar
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $tenant;
    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;
    public $productIdBeingEdited;

    // Form Fields
    public $name, $description, $price, $stock, $image;
    public $existingImage; // Untuk preview saat edit

    public function mount()
    {
        // Ambil toko milik user yang login
        $this->tenant = Auth::user()->tenant;
    }

    // Reset pagination jika search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::where('tenant_id', $this->tenant->id)
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.tenant.product-manager', [
            'products' => $products
        ])->layout('layouts.app'); // Pakai layout dashboard yang ada
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'description', 'price', 'stock', 'image', 'existingImage']);
        $this->isModalOpen = true;
        $this->isEditMode = false;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
        }

        Product::create([
            'tenant_id' => $this->tenant->id,
            'name' => $this->name,
            'slug' => \Illuminate\Support\Str::slug($this->name) . '-' . time(),
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'image_path' => $imagePath,
            'is_active' => true,
        ]);

        $this->dispatch('swal:success', message: 'Produk berhasil ditambahkan!');
        $this->closeModal();
    }

    public function edit($id)
    {
        $product = Product::where('tenant_id', $this->tenant->id)->findOrFail($id);

        $this->productIdBeingEdited = $id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->existingImage = $product->image_path;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $product = Product::where('tenant_id', $this->tenant->id)->findOrFail($this->productIdBeingEdited);

        if ($this->image) {
            // Hapus gambar lama jika ada
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $product->image_path = $this->image->store('products', 'public');
        }

        $product->update([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
        ]);

        $this->dispatch('swal:success', message: 'Produk berhasil diperbarui!');
        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    // Dipanggil dari SweetAlert JS
    #[\Livewire\Attributes\On('deleteConfirmed')]
    public function deleteProduct($id)
    {
        $product = Product::where('tenant_id', $this->tenant->id)->findOrFail($id);
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();

        $this->dispatch('swal:success', message: 'Produk dihapus.');
    }
}
