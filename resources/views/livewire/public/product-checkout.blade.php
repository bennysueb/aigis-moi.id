<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Checkout Belanja</h2>

        @if(empty($cart))
        <div class="bg-white p-12 rounded-lg shadow text-center">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <p class="text-lg text-gray-600 mb-4">Keranjang belanja Anda kosong.</p>
            <a href="/" class="text-blue-600 hover:underline">Kembali ke Beranda</a>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- List Item --}}
            <div class="md:col-span-2 space-y-4">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-semibold text-lg mb-4 border-b pb-2">Produk dari: {{ $tenantName }}</h3>

                    @foreach($cart as $id => $item)
                    <div class="flex items-center py-4 border-b last:border-0">
                        <div class="h-20 w-20 flex-shrink-0 border rounded bg-gray-100">
                            @if($item['image'])
                            <img src="{{ asset('storage/' . $item['image']) }}" class="h-full w-full object-cover rounded">
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="font-medium text-gray-900">{{ $item['name'] }}</h4>
                            <p class="text-gray-500 text-sm">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <div class="flex items-center">
                            <button wire:click="decrement({{ $id }})" class="p-1 bg-gray-200 rounded hover:bg-gray-300">-</button>
                            <span class="mx-3 w-8 text-center">{{ $item['quantity'] }}</span>
                            <button wire:click="increment({{ $id }})" class="p-1 bg-gray-200 rounded hover:bg-gray-300">+</button>
                        </div>
                        <div class="ml-4 text-right min-w-[100px]">
                            <div class="font-bold text-gray-900">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </div>
                            <button wire:click="removeItem({{ $id }})" class="text-xs text-red-500 hover:underline mt-1">Hapus</button>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Shipping Info --}}
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-semibold text-lg mb-4">Informasi Pengiriman</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap / Lokasi Penerimaan</label>
                        <textarea wire:model="shippingAddress" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Ambil di Booth saat Event, atau kirim ke Jl. Mawar..."></textarea>
                        @error('shippingAddress') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-lg shadow sticky top-4">
                    <h3 class="font-semibold text-lg mb-4">Ringkasan Belanja</h3>

                    <div class="flex justify-between mb-2 text-gray-600">
                        <span>Total Harga ({{ count($cart) }} barang)</span>
                        <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>

                    <div class="border-t mt-4 pt-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg text-gray-900">Total Tagihan</span>
                            <span class="font-bold text-xl text-blue-600">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button wire:click="processCheckout" wire:loading.attr="disabled" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                        <span wire:loading.remove>Bayar Sekarang</span>
                        <span wire:loading>Memproses...</span>
                    </button>

                    <p class="text-xs text-center text-gray-500 mt-3">
                        Pembayaran aman diproses oleh Midtrans.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Script Pembayaran Midtrans (Copy logic dari Fase 4) --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('start-payment', (event) => {
                const snapToken = event.token;
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        Swal.fire('Berhasil!', 'Pembayaran berhasil.', 'success').then(() => {
                            // Redirect ke halaman My Orders (Nanti dibuat di Fase 5C)
                            // Untuk sekarang redirect home dulu
                            window.location.href = "/dashboard";
                        });
                    },
                    onPending: function(result) {
                        alert("Menunggu pembayaran!");
                        window.location.href = "/dashboard";
                    },
                    onError: function(result) {
                        alert("Pembayaran gagal!");
                    },
                    onClose: function() {
                        alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                    }
                });
            });

            @this.on('swal:error', (event) => {
                Swal.fire('Oops...', event.message, 'error');
            });

            @this.on('swal:success', (event) => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: 'success',
                    title: event.message
                });
            });
        });
    </script>
</div>