{{--
  File: resources/views/public/gallery/show-album.blade.php
--}}
<x-app-layout>

    {{-- Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Galeri Album: {{ $album->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Header Konten (Judul & Tombol Kembali) --}}
                    <div class="flex justify-between items-center mb-8">
                        <h1 class="text-3xl font-bold">
                            {{ $album->name }}
                        </h1>

                        <a href="{{ route('public.gallery.index') }}"
                           class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 flex-shrink-0 ml-4">
                            &larr; Kembali ke Galeri
                        </a>
                    </div>

                    {{-- Grid Galeri --}}
                    @if(count($album->all_photos) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            
                            {{-- 
                                LOOPING UTAMA 
                                Menggunakan '$album->all_photos' (gabungan Local + Drive)
                                Variabel $photo sekarang berupa Array, bukan Object Model.
                            --}}
                            @foreach($album->all_photos as $photo)
                                <div class="relative group border rounded-lg overflow-hidden shadow-lg bg-gray-50">
                                    
                                    {{-- Link Fancybox --}}
                                    {{-- Kita akses array key: ['url'] dan ['name'] --}}
                                    <a href="{{ $photo['url'] }}" 
                                       data-fancybox="gallery" 
                                       data-caption="{{ $photo['name'] }}"
                                       class="block">
                                        
                                        {{-- Gambar Thumbnail --}}
                                        <img src="{{ $photo['thumb'] }}"
                                             alt="{{ $photo['name'] }}"
                                             loading="lazy"
                                             class="w-full h-48 object-cover group-hover:opacity-75 transition-opacity">
                                    </a>

                                    {{-- Caption Nama File --}}
                                    <div class="p-2 text-xs text-center text-gray-600 truncate bg-white" title="{{ $photo['name'] }}">
                                        {{ $photo['name'] }}
                                    </div>
                                    
                                    {{-- (Opsional) Badge Sumber: Drive/Local --}}
                                    {{-- 
                                    @if($photo['source'] == 'drive')
                                        <span class="absolute top-2 right-2 bg-green-500 text-white text-[9px] px-1.5 rounded shadow">Drive</span>
                                    @endif
                                    --}}
                                </div>
                            @endforeach

                        </div>
                    @else
                        {{-- Pesan Kosong --}}
                        <div class="border-2 border-dashed border-gray-300 rounded-lg h-64 p-4 text-center flex items-center justify-center">
                            <p class="text-gray-500">
                                Album ini tidak memiliki media untuk ditampilkan.
                            </p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Script Fancybox --}}
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
        <script>
            Fancybox.bind("[data-fancybox]", {
                // Opsi tambahan Fancybox bisa ditaruh sini
            });
        </script>
    @endpush

</x-app-layout>