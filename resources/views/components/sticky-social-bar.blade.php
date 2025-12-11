@php
// Mengambil semua data dinamis menggunakan helper
$stickyData = \App\Helpers\StickyBarHelper::getData();
$links = $stickyData['links'] ?? [];
$gallery = $stickyData['gallery'] ?? null;
@endphp

<div x-data="{
    cameraPopupOpen: false,
    isBarVisible: true,
    activeVideoUrl: '', // 1. Mulai dengan string kosong
    initialVideoUrl: '{{ $gallery && $gallery->videos->first() ? $gallery->videos->first()->youtube_embed_url : '' }}',
    openGallery() {
        this.activeVideoUrl = this.initialVideoUrl; 
        this.cameraPopupOpen = true;
    },
    closeGallery() {
        this.cameraPopupOpen = false;
        this.activeVideoUrl = ''; 
    }
}" class="z-[9999]">

    {{--
    ======================================================================
    PERBAIKAN 1 (Toggle Desktop) & PERBAIKAN 2 (Padding/Jarak)
    
    1. Logika `:class` di bawah ini memperbaiki bug show/hide di desktop.
    2. Saya mengubah `md:py-4` menjadi `md:py-6`. INI adalah cara kamu
       mengontrol "jarak atas dan bawah". Ganti '6' ke angka yang kamu suka.
       (cth: md:py-2, md:py-4, md:py-8, dll.)
    
    3. Saya MENGHAPUS `md:justify-center` karena tinggi bar ini
       seharusnya 'auto' dan diatur HANYA oleh padding (md:py-6).
    ======================================================================
    --}}
    <div
        class="fixed left-1/2 -translate-x-1/2 transform md:top-1/2 md:left-0 md:-translate-y-1/2 flex md:flex-col items-center md:justify-center gap-1 bg-gray-900 bg-opacity-80 backdrop-blur-sm p-3 md:ps-4 md:py-6 rounded-t-lg md:rounded-none md:rounded-tr-xl md:rounded-br-xl shadow-lg transition-transform duration-300 ease-in-out"
        :class="{
            'translate-y-full': !isBarVisible,
            'md:translate-x-0': isBarVisible,
            'md:-translate-x-full': !isBarVisible
        }">

        {{-- 1. Link "Getting There" --}}
        @if(!empty($links['getting_there_url']))
        <a href="{{ $links['getting_there_url'] }}" target="_blank" rel="noopener noreferrer" title="Getting There" class="group relative w-10 h-10 flex items-center justify-center bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 hover:opacity-90 transition-opacity duration-300">
            <i class="fas fa-map-marker-alt fa-sm"></i>
            <span class="absolute left-full px-3 py-1 bg-blue-500 text-white text-base whitespace-nowrap hidden md:block opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 ease-in-out pointer-events-none group-hover:pointer-events-auto">
                <h3 class="ml-6">Getting There</h3>
            </span>
        </a>
        @endif

        {{-- 2. Link Wikipedia --}}
        @if(!empty($links['wikipedia_url']))
        <a href="{{ $links['wikipedia_url'] }}" target="_blank" rel="noopener noreferrer" title="Wikipedia" class="group relative w-10 h-10 flex items-center justify-center bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 hover:opacity-90 transition-opacity duration-300">
            <i class="fab fa-wikipedia-w fa-sm"></i>
            <span class="absolute left-full px-3 py-1 bg-gray-200 text-gray-800 text-base whitespace-nowrap hidden md:block opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 ease-in-out pointer-events-none group-hover:pointer-events-auto">
                <h3 class="ml-6">Wikipedia</h3>
            </span>
        </a>
        @endif

        {{-- 3. Link Instagram --}}
        @if(!empty($links['instagram_url']))
        <a href="{{ $links['instagram_url'] }}" target="_blank" rel="noopener noreferrer" title="Instagram" class="group relative w-10 h-10 flex items-center justify-center bg-gradient-to-br from-pink-500 to-yellow-500 text-white rounded-md hover:opacity-90 transition-opacity duration-300">
            <i class="fab fa-instagram fa-sm"></i>
            <span class="absolute left-full px-3 py-1 bg-gradient-to-br from-pink-500 to-yellow-500 text-white text-base whitespace-nowrap hidden md:block opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 ease-in-out pointer-events-none group-hover:pointer-events-auto">
                <h3 class="ml-6">Instagram</h3>
            </span>
        </a>
        @endif

        {{-- 4. Link YouTube Channel --}}
        @if(!empty($links['youtube_url']))
        <a href="{{ $links['youtube_url'] }}" target="_blank" rel="noopener noreferrer" title="YouTube" class="group relative w-10 h-10 flex items-center justify-center bg-red-600 text-white rounded-md hover:bg-red-700 hover:opacity-90 transition-opacity duration-300">
            <i class="fab fa-youtube fa-sm"></i>
            <span class="absolute left-full px-3 py-1 bg-red-600 text-white text-base whitespace-nowrap hidden md:block opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 ease-in-out pointer-events-none group-hover:pointer-events-auto">
                <h3 class="ml-6">YouTube</h3>
            </span>
        </a>
        @endif

        {{-- 5. Link WhatsApp --}}
        @if(!empty($links['whatsapp_url']))
        <a href="{{ $links['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" title="WhatsApp" class="group relative w-10 h-10 flex items-center justify-center bg-green-500 text-white rounded-md hover:bg-green-600 hover:opacity-90 transition-opacity duration-300">
            <i class="fab fa-whatsapp fa-sm"></i>
            <span class="absolute left-full px-3 py-1 bg-green-500 text-white text-base whitespace-nowrap hidden md:block opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 ease-in-out pointer-events-none group-hover:pointer-events-auto">
                <h3 class="ml-6">WhatsApp</h3>
            </span>
        </a>
        @endif

        {{-- 6. Link Microsite --}}
        @if(!empty($links['microsite_url']))
        <a href="{{ $links['microsite_url'] }}" target="_blank" rel="noopener noreferrer" title="zlinks.id" class="group relative w-10 h-10 flex items-center justify-center bg-gradient-to-br from-yellow-500 to-pink-500 text-white rounded-md hover:opacity-90 transition-opacity duration-300">
            <i class="fab fa-staylinked fa-sm"></i>
            <span class="absolute left-full px-3 py-1 bg-gradient-to-br from-pink-500 to-yellow-500 text-white text-base whitespace-nowSrap hidden md:block opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 ease-in-out pointer-events-none group-hover:pointer-events-auto">
                <h3 class="ml-6">Microsite</h3>
            </span>
        </a>
        @endif

        {{-- 7. Tombol Galeri --}}
        @if($gallery && $gallery->videos->isNotEmpty())
        <button @click="openGallery()" title="Galeri" class="group relative w-10 h-10 flex items-center justify-center bg-purple-500 text-white rounded-md hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-300 hover:opacity-90 transition-opacity duration-300">
            <i class="fas fa-camera fa-sm"></i>
            <span class="absolute left-full px-3 py-1 bg-purple-500 text-white text-base whitespace-nowrap hidden md:block opacity-0 -translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 ease-in-out pointer-events-none group-hover:pointer-events-auto">
                <h3 class="ml-6">Galeri</h3>
            </span>
        </button>
        @endif
    </div>

    {{-- Tombol Toggle (Sudah benar) --}}
    <button
        @click="isBarVisible = !isBarVisible"
        title="Toggle social bar"
        class="fixed left-1/2 -translate-x-1/2 transform md:-translate-y-1/2 md:translate-x-0 w-10 h-10 bg-gray-800 text-white flex items-center justify-center shadow-lg hover:bg-gray-700 transition-all duration-300 ease-in-out rounded-full"
        :class="{ 'bottom-[84px] md:top-1/2 md:left-[76px]': isBarVisible, 'bottom-2 md:top-1/2 md:left-2': !isBarVisible }">
        <span class="relative flex items-center justify-center w-full h-full">
            <i class="fas fa-times absolute" x-show="isBarVisible" x-transition.opacity.duration.200ms></i>
            <i class="fas fa-chevron-right absolute" x-show="!isBarVisible" x-transition.opacity.duration.200ms></i>
        </span>
    </button>

    {{-- Popup Galeri Video (Sudah benar) --}}
    @if($gallery && $gallery->videos->isNotEmpty())
    <div x-show="cameraPopupOpen" x-transition.opacity style="display: none;" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-50">
        <div
            class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col"
            @click.away="closeGallery()">
            <header class="flex justify-between items-center p-4 border-b">
                <h2 class="text-xl font-bold text-gray-800">{{ $gallery->title }}</h2>
                <button @click="closeGallery()" class="text-gray-500 hover:text-gray-800">&times;</button>
            </header>
            <main class="flex-grow p-4 overflow-y-auto">
                <div class="aspect-w-16 aspect-h-9 mb-4">
                    <iframe :src="activeVideoUrl" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($gallery->videos as $video)
                    <button
                        @click="activeVideoUrl = '{{ $video->youtube_embed_url }}'"
                        :class="{ 'bg-blue-600 text-white': activeVideoUrl === '{{ $video->youtube_embed_url }}', 'bg-gray-200 text-gray-700 hover:bg-gray-300': activeVideoUrl !== '{{ $video->youtube_embed_url }}' }"
                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors">
                        {{ $video->series_title }}
                    </button>
                    @endforeach
                </div>
            </main>
        </div>
    </div>
    @endif
</div>