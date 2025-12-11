<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Header Toko --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8 flex items-center gap-6">
            <div class="h-24 w-24 flex-shrink-0">
                @if($tenant->logo_path)
                <img src="{{ asset('storage/' . $tenant->logo_path) }}" class="h-full w-full object-cover rounded-lg border">
                @else
                <div class="h-full w-full bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">No Logo</div>
                @endif
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $tenant->name }}</h1>
                <p class="text-gray-600 mt-1">{{ $tenant->description }}</p>
                <div class="mt-2 text-sm text-gray-500 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ $tenant->address ?? 'Lokasi Event' }}
                </div>
            </div>
        </div>

        {{-- Search --}}
        <div class="mb-6">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari produk di toko ini..." class="w-full md:w-1/3 border-gray-300 rounded-full px-4 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Grid Produk --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($products as $product)
            <div class="bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden flex flex-col">
                <div class="aspect-w-1 aspect-h-1 w-full bg-gray-200 relative">
                    @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="object-cover w-full h-48">
                    @else
                    <div class="w-full h-48 flex items-center justify-center text-gray-400">No Image</div>
                    @endif

                    @if($product->stock <= 0)
                        <div class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
                        <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">Habis</span>
                </div>
                @endif
            </div>

            <div class="p-4 flex-1 flex flex-col">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $product->name }}</h3>
                <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ $product->description }}</p>

                <div class="mt-auto flex items-center justify-between">
                    <span class="text-blue-600 font-bold text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</span>

                    <button wire:click="addToCart({{ $product->id }})"
                        class="p-2 bg-gray-100 rounded-full hover:bg-blue-600 hover:text-white transition disabled:opacity-50"
                        @if($product->stock <= 0) disabled @endif>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-gray-500">
            Belum ada produk yang tersedia saat ini.
        </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>

{{-- Floating Cart Button (Shortcut ke Checkout) --}}
@if(count(session('cart', [])) > 0)
<a href="{{ route('shop.checkout') }}" class="fixed bottom-8 right-8 bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 flex items-center gap-2 z-50 animate-bounce">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
    </svg>
    <span class="font-bold">{{ count(session('cart')) }} Item</span>
</a>
@endif
</div>