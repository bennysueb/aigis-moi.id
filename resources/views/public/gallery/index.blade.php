{{--
  File: resources/views/public/gallery/index.blade.php
--}}
<x-app-layout>

    {{-- Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Galeri Album') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden"> 
                <div class="p-6 md:p-8 text-gray-900">

                    {{-- Judul Halaman --}}
                    <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">
                        Album Galeri Kami
                    </h1>

                    @if($albums->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                            @foreach($albums as $album)
                                {{-- LOGIC: Ambil foto pertama sebagai Cover --}}
                                @php
                                    // Menggunakan aksesor 'all_photos' yang sudah kita buat di Model
                                    // Ini otomatis menggabungkan foto Lokal + Drive dan mengurutkannya
                                    $coverPhoto = $album->all_photos->first();
                                    
                                    // Tentukan URL Cover (Jika kosong, pakai placeholder warna abu)
                                    $coverUrl = $coverPhoto ? $coverPhoto['thumb'] : null;
                                    $photoCount = $album->all_photos->count();
                                @endphp

                                {{-- KARTU ALBUM --}}
                                <a href="{{ route('public.gallery.album', $album->slug) }}"
                                   class="group block rounded-xl overflow-hidden bg-white border border-gray-200 shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                                    
                                    {{-- Wrapper Gambar (Aspect Ratio 4:3) --}}
                                    <div class="relative h-64 bg-gray-100 overflow-hidden">
                                        
                                        @if($coverUrl)
                                            {{-- Gambar Cover --}}
                                            <img src="{{ $coverUrl }}" 
                                                 alt="{{ $album->name }}" 
                                                 loading="lazy"
                                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                        @else
                                            {{-- Placeholder (Jika Album Kosong) --}}
                                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                                <svg class="w-16 h-16 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <span class="text-xs uppercase tracking-wider font-semibold">Album Kosong</span>
                                            </div>
                                        @endif

                                        {{-- Overlay Gelap saat Hover --}}
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                            <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                                <span class="inline-flex items-center px-4 py-2 bg-white/90 rounded-full text-sm font-semibold text-gray-800 shadow-sm backdrop-blur-sm">
                                                    Lihat Album
                                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Informasi Album --}}
                                    <div class="p-5">
                                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-2 line-clamp-1" title="{{ $album->name }}">
                                            {{ $album->name }}
                                        </h3>
                                        
                                        <div class="flex items-center justify-between text-sm text-gray-500">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                {{ $photoCount }} Foto
                                            </span>
                                            <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                                                {{ $album->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                        </div>
                    @else
                        {{-- Empty State (Belum ada album) --}}
                        <div class="flex flex-col items-center justify-center py-20 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 text-center">
                            <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <h3 class="text-lg font-medium text-gray-900">Belum Ada Album</h3>
                            <p class="text-gray-500 max-w-sm mt-1">Galeri foto belum tersedia saat ini. Silakan kembali lagi nanti.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</x-app-layout>