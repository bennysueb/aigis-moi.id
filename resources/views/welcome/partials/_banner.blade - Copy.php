@if(isset($items) && $items->count() > 0)
<div class="w-full bg-gray-100">
    <section
        {{-- 
            Inisialisasi AlpineJS. 
            hoveredSlide: 1 berarti panel pertama aktif secara default.
        --}}
        x-data="{ hoveredSlide: 1 }"
        @mouseleave="hoveredSlide = 1" {{-- Kembali ke panel 1 saat mouse keluar --}}
        {{-- 
            Untuk Mobile: Aktifkan flex dan overflow-x-auto untuk horizontal scroll.
            Untuk Desktop: Atur tinggi dan nonaktifkan scroll.
        --}}
        class="flex w-full h-[550px] space-x-2 overflow-x-auto lg:overflow-x-hidden">
        {{-- Loop untuk setiap panel banner --}}
        @foreach($items as $banner)
        <div
            @mouseover="hoveredSlide = {{ $loop->iteration }}"
            {{-- 
                Untuk Mobile: Setiap panel memiliki lebar tetap 320px.
                Untuk Desktop: Lebar dikontrol oleh AlpineJS.
            --}}
            class="relative flex-shrink-0 w-80 lg:w-auto h-full bg-cover bg-center transition-all duration-700 ease-in-out cursor-pointer"
            :class="{
                'flex-grow-[2.5]': hoveredSlide === {{ $loop->iteration }}, {{-- Panel aktif: 2.5x lebih besar --}}
                'flex-grow-[1] opacity-80': hoveredSlide !== {{ $loop->iteration }} {{-- Panel tidak aktif: redup --}}
            }"
            style="background-image: url('{{ $banner->desktop_image_url }}')">
            {{-- Lapisan Gradient Overlay yang aktif saat hover --}}
            <div
                class="absolute inset-0 w-full h-full transition-opacity duration-500"

                {{-- Atur style secara dinamis dengan Alpine.js --}}
                :style="{
        'background-image': 'linear-gradient(to top, {{ $banner->gradient_from ?? 'rgba(0,0,0,0.8)' }}, {{ $banner->gradient_to ?? 'rgba(0,0,0,0.1)' }})',
        'opacity': hoveredSlide === {{ $loop->iteration }} ? {{ $banner->opacity ?? 0.75 }} : 0
    }">
            </div>

            {{-- Konten Teks --}}
            <div class="relative w-full h-full flex flex-col justify-end p-6 text-white overflow-hidden">

                {{-- 1. Blok Subtitle (Terlihat saat tidak di-hover) --}}
                <div class="transition-opacity duration-300"
                    :class="hoveredSlide !== {{ $loop->iteration }} ? 'opacity-100' : 'opacity-0'">
                    <p class="text-lg font-medium drop-shadow-sm">{{ $banner->subtitle }}</p>
                </div>

                {{-- 2. Blok Konten Utama (Terlihat saat di-hover) --}}
                <div class="absolute bottom-6 left-6 right-6 transition-opacity duration-300"
                    :class="hoveredSlide === {{ $loop->iteration }} ? 'opacity-100' : 'opacity-0'">

                    <h2 class="text-3xl font-bold font-heading drop-shadow-md">{{ $banner->headline }}</h2>

                    {{-- Menampilkan Fitur dari data JSON --}}
                    @if(!empty($banner->features))
                    <ul class="mt-2">
                        @foreach($banner->features as $feature)
                        <li class="text-base drop-shadow-sm pt-1 flex items-center">
                            {{-- Icon checklist sederhana --}}
                            <svg class="w-4 h-4 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    @if($banner->button_text && $banner->button_link)
                    <div class="mt-4">
                        <a href="{{ $banner->button_link }}" class="inline-block bg-white text-secondary-dark font-bold py-2 px-4 rounded-md text-sm hover:bg-gray-200 transition-colors duration-300 shadow-md">
                            {{ $banner->button_text }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </section>
</div>
@endif