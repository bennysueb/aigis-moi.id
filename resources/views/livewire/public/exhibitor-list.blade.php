<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Exhibitors') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($exhibitors as $exhibitor)
                <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col transition-all duration-300 hover:scale-105 border-2 {{ in_array($exhibitor->id, $scannedExhibitorIds) ? 'border-green-500' : 'border-gray-200' }}" wire:key="exhibitor-{{ $exhibitor->id }}">

                    {{-- ====================================================== --}}
                    {{-- BAGIAN INI DIMODIFIKASI UNTUK MENAMPILKAN LOGO --}}
                    {{-- ====================================================== --}}
                    <a href="#">
                        @if ($exhibitor->logo_path)
                        {{-- Jika logo ada, tampilkan --}}
                        <img src="{{ asset('storage/' . $exhibitor->logo_path) }}" alt="Logo {{ $exhibitor->nama_instansi }}" class="w-full h-48 object-contain p-4 bg-white">
                        @else
                        {{-- Jika tidak ada, tampilkan placeholder --}}
                        <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-building text-4xl text-gray-400"></i>
                        </div>
                        @endif
                    </a>

                    <div class="p-6 flex flex-col flex-grow">
                        @if ($exhibitor->booth_number)
                        <span class="bg-blue-100 text-blue-800 text-2xl text-center font-semibold rounded mb-2 px-3 py-1 inline-block">
                            No. Booth: {{ $exhibitor->booth_number }}
                        </span>
                        @endif
                        <h3 class="text-lg font-bold text-gray-900">{{ $exhibitor->nama_instansi }}</h3>
                        <p class="text-sm text-gray-600 mt-2 flex-grow">
                            {{-- Tampilkan deskripsi, atau pesan default jika kosong --}}
                            {{ Str::limit($exhibitor->description, 100) ?: 'No description available.' }}
                        </p>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('exhibitors.show', $exhibitor->uuid) }}" class="text-blue-600 font-semibold hover:text-blue-800">
                                View Profile &rarr;
                            </a>
                        </div>

                        <div>
                            @if (in_array($exhibitor->id, $scannedExhibitorIds))
                            <span class="mt-4 inline-block bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded">
                                You have visited this booth.
                            </span>
                            @else
                            <span class="mt-4 inline-block bg-gray-100 text-gray-800 text-sm font-semibold px-3 py-1 rounded">
                                You have not visited this booth yet.
                            </span>
                            @endif

                            @if(Auth::check() && in_array($exhibitor->id, $scannedExhibitorIds))
                            <div class="mt-4 flex justify-between items-center">
                                {{-- Sistem Rating Bintang --}}
                                <div class="flex space-x-1" x-data="{
                                rating: {{ $favoritesData[$exhibitor->id]['rating'] ?? 0 }},
                                hoverRating: 0,
                                setRating(rate) {
                                    this.rating = rate;
                                    $wire.setRating({{ $exhibitor->id }}, rate);
                                }
                            }">
                                    <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                        <button @click="setRating(star)" @mouseover="hoverRating = star" @mouseleave="hoverRating = 0"
                                            class="text-2xl transition-colors">
                                            <i class="fas fa-star" :class="{
                            'text-yellow-400': hoverRating >= star || rating >= star,
                            'text-gray-300': hoverRating < star && rating < star
                        }"></i>
                                        </button>
                                    </template>
                                </div>

                                {{-- Tombol Love --}}
                                <button wire:click="toggleLove({{ $exhibitor->id }})" class="text-2xl transition-transform hover:scale-125">
                                    @if(isset($favoritesData[$exhibitor->id]) && $favoritesData[$exhibitor->id]['is_loved'])
                                    <i class="fas fa-heart text-red-500"></i>
                                    @else
                                    <i class="far fa-heart text-gray-400"></i>
                                    @endif
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <p class="col-span-3 text-center text-gray-500">No exhibitors have been listed yet.</p>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $exhibitors->links() }}
            </div>
        </div>
    </div>
</div>