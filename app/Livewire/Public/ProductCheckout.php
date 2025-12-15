<?php

namespace App\Livewire\Public;

use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductOrderItem;
use App\Models\Tenant; // Tambahkan ini
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductCheckout extends Component
{
    public $cart = [];
    public $totalAmount = 0;
    public $shippingAddress;
    public $tenantName;

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = session()->get('cart', []);

        // Hitung total
        $this->totalAmount = 0;
        $tenantId = null;

        foreach ($this->cart as $item) {
            $this->totalAmount += ($item['price'] * $item['quantity']);
            $tenantId = $item['tenant_id'];
        }

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            $this->tenantName = $tenant ? $tenant->name : 'Toko';
        }
    }

    public function increment($productId)
    {
        $product = Product::find($productId);
        if (isset($this->cart[$productId]) && $this->cart[$productId]['quantity'] < $product->stock) {
            $this->cart[$productId]['quantity']++;
            $this->updateSession();
        } else {
            $this->dispatch('swal:error', message: 'Stok maksimal tercapai.');
        }
    }

    public function decrement($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] > 1) {
                $this->cart[$productId]['quantity']--;
            } else {
                unset($this->cart[$productId]); // Hapus jika 0
            }
            $this->updateSession();
        }
    }

    public function removeItem($productId)
    {
        unset($this->cart[$productId]);
        $this->updateSession();
    }

    public function updateSession()
    {
        session()->put('cart', $this->cart);
        $this->loadCart();
        $this->dispatch('cart-updated');
    }

    public function processCheckout(TransactionService $transactionService)
    {
        if (empty($this->cart)) {
            return;
        }

        $this->validate([
            'shippingAddress' => 'required|string|min:10',
        ]);

        if (!Auth::check()) {
            // Redirect login jika belum login (simpan return url)
            session(['url.intended' => route('shop.checkout')]);
            return redirect()->route('login');
        }

        try {
            $snapToken = DB::transaction(function () use ($transactionService) {
                $user = Auth::user();

                // Ambil tenant ID dari item pertama
                $firstItem = reset($this->cart);
                $tenantId = $firstItem['tenant_id'];

                // 1. Buat Order Header
                $order = ProductOrder::create([
                    'user_id' => $user->id,
                    'tenant_id' => $tenantId,
                    'total_amount' => $this->totalAmount,
                    'final_amount' => $this->totalAmount, // Nanti dikurangi diskon/voucher jika ada
                    'shipping_address' => $this->shippingAddress,
                    'status' => 'pending',
                ]);

                // 2. Buat Order Items & Kurangi Stok (Optimistic locking simple)
                foreach ($this->cart as $item) {
                    $product = Product::lockForUpdate()->find($item['id']);

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stok untuk produk {$product->name} tidak mencukupi.");
                    }

                    ProductOrderItem::create([
                        'product_order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price_at_purchase' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);

                    // Kurangi stok
                    $product->decrement('stock', $item['quantity']);
                }

                // 3. Panggil Midtrans Service (Fase 3)
                $transaction = $transactionService->createTransaction($user, $order, $this->totalAmount);

                return $transaction->snap_token;
            });

            // Kosongkan keranjang setelah berhasil generate order
            session()->forget('cart');
            $this->dispatch('cart-updated');

            // Trigger Popup Bayar
            $this->dispatch('start-payment', token: $snapToken);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.public.product-checkout')->layout('layouts.app');
    }
}
