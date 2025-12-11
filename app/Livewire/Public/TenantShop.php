<?php

namespace App\Livewire\Public;

use App\Models\Tenant;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class TenantShop extends Component
{
    use WithPagination;

    public $tenantSlug;
    public Tenant $tenant;
    public $search = '';

    public function mount($slug)
    {
        $this->tenantSlug = $slug;
        $this->tenant = Tenant::where('slug', $slug)->where('status', 'active')->firstOrFail();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        // 1. Validasi Stok
        if (!$product || $product->stock < 1) {
            $this->dispatch('swal:error', message: 'Stok produk habis!');
            return;
        }

        // 2. Validasi Multi-Toko (Cegah keranjang campur aduk antar toko)
        $cart = session()->get('cart', []);
        $firstItem = reset($cart);

        if (!empty($cart) && isset($firstItem['tenant_id']) && $firstItem['tenant_id'] != $this->tenant->id) {
            $this->dispatch('swal:error', message: 'Selesaikan checkout toko sebelumnya dulu, atau kosongkan keranjang.');
            return;
        }

        // 3. Tambah ke Session Cart
        if (isset($cart[$productId])) {
            // Jika sudah ada, cek apakah stok cukup untuk nambah
            if ($cart[$productId]['quantity'] + 1 > $product->stock) {
                $this->dispatch('swal:error', message: 'Stok tidak mencukupi.');
                return;
            }
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image_path,
                'quantity' => 1,
                'tenant_id' => $this->tenant->id
            ];
        }

        session()->put('cart', $cart);

        // 4. Update Counter di Navbar
        $this->dispatch('cart-updated');
        $this->dispatch('swal:success', message: 'Masuk keranjang!');
    }

    public function render()
    {
        $products = Product::where('tenant_id', $this->tenant->id)
            ->where('is_active', true)
            ->where('name', 'like', '%' . $this->search . '%')
            ->paginate(12);

        return view('livewire.public.tenant-shop', [
            'products' => $products
        ])->layout('layouts.app'); // Menggunakan layout utama
    }
}
