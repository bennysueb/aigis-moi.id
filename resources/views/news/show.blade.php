<x-app-layout>
    @php $locale = app()->getLocale(); @endphp
    <div class="w-full">

        <div class="bg-gray-100 py-8">
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-12 gap-8 items-center">

                    {{-- === KOLOM IKLAN KIRI === --}}
                    <div class="hidden lg:block col-span-2 h-96">
                        @if ($leftAd && $leftAd->hasMedia())
                        <a href="{{ $leftAd->url ?? '#' }}" target="_blank" class="block w-full h-full rounded-lg overflow-hidden group relative">
                            {{-- Gunakan 'ad-tall' seperti di file events/index.blade.php --}}
                            <img src="{{ $leftAd->getFirstMediaUrl('default', 'ad-tall') }}" alt="{{ $leftAd->headline }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 bg-white/10 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="absolute bottom-0 left-0 p-4 text-white">
                                <h4 class="font-bold drop-shadow-md">{{ $leftAd->headline }}</h4>
                            </div>
                        </a>
                        @else
                        <div class="w-full h-full bg-gray-200 rounded-lg"></div>
                        @endif
                    </div>

                    {{-- === KOLOM SLIDER TENGAH === --}}
                    <div class="col-span-12 lg:col-span-8">
                        @if(isset($featuredPosts) && $featuredPosts->isNotEmpty())
                        <section
                            class="relative"
                            x-data="{ swiper: null }"
                            x-init="
                    swiper = new Swiper($refs.container, {
                        loop: true,
                        slidesPerView: 1,
                        spaceBetween: 30,
                        autoplay: {
                            delay: 5000,
                            disableOnInteraction: false,
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                    });
                ">
                            {{-- Hanya tambahkan header ini di show.blade.php --}}
                            @if(Route::currentRouteName() == 'news.show')
                            <div class="flex justify-between items-center gap-x-4 gap-y-2 mb-4">
                                <h2 class="text-2xl font-bold font-serif text-gray-800">Featured Post</h2>
                                <a href="{{ route('news.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                    &larr; Kembali ke Seluruh Berita
                                </a>
                            </div>
                            @endif

                            {{-- Kontainer Slider --}}
                            <div class="swiper-container rounded-lg shadow-xl overflow-hidden" x-ref="container">
                                <div class="swiper-wrapper">
                                    {{-- Loop untuk setiap slide --}}
                                    @foreach($featuredPosts as $featuredPost)
                                    <div class="swiper-slide">
                                        <a href="{{ route('news.show', $featuredPost) }}" class="block group">
                                            <div class="relative bg-gray-800">
                                                {{-- Slider Image (Hybrid) --}}
                                                @php
                                                    $sliderImg = $featuredPost->featured_image_drive_id 
                                                        ? route('media.stream.public', ['path' => $featuredPost->featured_image_drive_id]) 
                                                        : $featuredPost->getFirstMediaUrl('featured', 'page-banner');
                                                @endphp
                                                <img src="{{ $sliderImg ?: 'https://via.placeholder.com/800x400' }}" class="w-full h-96 object-cover opacity-50 group-hover:opacity-75 transition-opacity duration-300">
                                                
                                                <div class="absolute bottom-0 left-0 p-8">
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($featuredPost->categories as $category)
                                                        <a href="{{ route('news.index', ['category' => $category->slug]) }}" class="px-3 py-1 bg-accent text-secondary-dark text-sm font-semibold rounded-full hover:bg-white transition-colors">
                                                            {{ $category->getTranslation('name', $locale) }}
                                                        </a>
                                                        @endforeach
                                                    </div>
                                                    <h1 class="mt-4 text-3xl md:text-4xl font-bold font-serif text-white group-hover:text-gray-200">
                                                        {{ $featuredPost->getTranslation('title', $locale) }}
                                                    </h1>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Tombol Navigasi (Kode Anda sudah benar) --}}
                            <div class="swiper-button-prev absolute top-1/2 -translate-y-1/2 left-4 text-white bg-black/30 hover:bg-black/50 p-2 rounded-full transition-colors cursor-pointer z-10">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </div>
                            <div class="swiper-button-next absolute top-1/2 -translate-y-1/2 right-4 text-white bg-black/30 hover:bg-black/50 p-2 rounded-full transition-colors cursor-pointer z-10">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>

                            {{-- Paginasi (Titik-titik) --}}
                            {{-- PERBAIKAN: Hapus 'left-1/2' dan '-translate-x-1/2' --}}
                            <div class="swiper-pagination absolute bottom-4 z-10"></div>
                        </section>
                        @endif
                    </div>

                    {{-- === KOLOM IKLAN KANAN === --}}
                    <div class="hidden lg:block col-span-2 h-96">
                        @if ($rightAd && $rightAd->hasMedia())
                        <a href="{{ $rightAd->url ?? '#' }}" target="_blank" class="block w-full h-full rounded-lg overflow-hidden group relative">
                            {{-- Gunakan 'ad-tall' seperti di file events/index.blade.php --}}
                            <img src="{{ $rightAd->getFirstMediaUrl('default', 'ad-tall') }}" alt="{{ $rightAd->headline }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 bg-white/10 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="absolute bottom-0 left-0 p-4 text-white">
                                <h4 class="font-bold drop-shadow-md">{{ $rightAd->headline }}</h4>
                            </div>
                        </a>
                        @else
                        <div class="w-full h-full bg-gray-200 rounded-lg"></div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        <div class="bg-gray-50 py-12">



            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-8">

                {{-- Kolom Kiri: Konten Utama Berita --}}
                <div class="w-full lg:w-2/3">

                    <article class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6 md:p-10">
                            {{-- KATEGORI --}}
                            <div class="flex justify-between items-center gap-x-4 gap-y-2 mb-4">
                                @foreach($post->categories as $category)
                                <a href="{{ route('news.index', ['category' => $category->slug]) }}" class="text-base font-semibold text-indigo-600 uppercase tracking-wide hover:text-indigo-800">
                                    {{ $category->name }}
                                </a>
                                @endforeach

                            </div>

                            {{-- JUDUL --}}
                            <h1 class="mt-2 text-3xl md:text-4xl font-extrabold font-serif text-gray-900">
                                {{ $post->title }}
                            </h1>

                            {{-- META INFO: PENULIS & TANGGAL --}}
                            <div class="mt-4 flex items-center text-sm text-gray-500">
                                <span>By {{ $post->author->name ?? 'Staff' }}</span>
                                <span class="mx-2">&bull;</span>
                                <span>{{ $post->published_at->locale(app()->getLocale())->translatedFormat('l, F d, Y') }}</span>
                            </div>

                            {{-- GAMBAR UNGGULAN (HYBRID) --}}
                            @if($post->featured_image_drive_id || $post->hasMedia('featured'))
                            <figure class="mt-6 mb-8">
                                @php
                                    $mainImage = $post->featured_image_drive_id 
                                        ? route('media.stream.public', ['path' => $post->featured_image_drive_id]) 
                                        : $post->getFirstMediaUrl('featured');
                                @endphp
                                <img src="{{ $mainImage }}" alt="{{ $post->title }}" class="w-full h-auto object-cover rounded-lg shadow-md">
                            </figure>
                            @endif

                            {{-- TOMBOL SHARE SOSIAL MEDIA --}}
                            <div class="flex space-x-3 mb-8">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center text-sm"><i class="fab fa-facebook-f mr-2"></i> Share</a>
                                <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ urlencode($post->title) }}" target="_blank" class="px-4 py-2 bg-blue-400 text-white rounded-md hover:bg-blue-500 flex items-center text-sm"><i class="fab fa-twitter mr-2"></i> Tweet</a>
                                <a href="whatsapp://send?text={{ urlencode($post->title . ' ' . url()->current()) }}" data-action="share/whatsapp/share" target="_blank" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 flex items-center text-sm"><i class="fab fa-whatsapp mr-2"></i> WhatsApp</a>
                            </div>

                            {{-- ========================================================== --}}
                            {{-- KONTEN DINAMIS BERDASARKAN TIPE (PERUBAHAN DIMULAI DI SINI) --}}
                            {{-- ========================================================== --}}
                            <div class="mt-8 text-gray-800 leading-relaxed text-lg">

                                @if($post->type === 'article')
                                <div class="prose max-w-none">
                                    {!! $post->content !!}
                                </div>
                                {{-- Info Sumber Berita --}}
                                @if($post->source_url)
                                <div class="mt-8 pt-4 border-t text-sm text-gray-500">
                                    Source: <a href="{{ $post->source_url }}" target="_blank" class="text-indigo-600 hover:underline">{{ $post->source_name ?? $post->source_url }}</a>
                                </div>
                                @endif

                                @elseif($post->type === 'video')
                                <div class="aspect-w-16 aspect-h-9 w-full">
                                    @php
                                    $embedUrl = '';
                                    if (Str::contains($post->media_url, 'youtube.com') || Str::contains($post->media_url, 'youtu.be')) {
                                    $videoId = '';
                                    parse_str( parse_url( $post->media_url, PHP_URL_QUERY ), $params );
                                    if (isset($params['v'])) {
                                    $videoId = $params['v'];
                                    } else if (Str::contains($post->media_url, 'youtu.be')) {
                                    $videoId = Str::after($post->media_url, 'youtu.be/');
                                    }
                                    $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                                    } elseif (Str::contains($post->media_url, 'vimeo.com')) {
                                    $videoId = Str::afterLast($post->media_url, '/');
                                    $embedUrl = "https://player.vimeo.com/video/{$videoId}";
                                    }
                                    @endphp
                                    @if($embedUrl)
                                    <iframe src="{{ $embedUrl }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    @else
                                    <p class="text-red-500">Could not embed video from URL: {{ $post->media_url }}</p>
                                    @endif
                                </div>
                                <div class="mt-6 prose max-w-none">
                                    {!! $post->content !!} {{-- Menampilkan deskripsi video jika ada --}}
                                </div>

                                @elseif($post->type === 'audio')
                                <audio controls class="w-full">
                                    <source src="{{ $post->media_url }}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                                <div class="mt-6 prose max-w-none">
                                    {!! $post->content !!} {{-- Menampilkan deskripsi audio jika ada --}}
                                </div>

                                @elseif($post->type === 'press_release')
                                @php
                                $documentUrl = $post->getFirstMediaUrl('document');
                                @endphp
                                @if($documentUrl)
                                <div class="bg-gray-100 border-l-4 border-indigo-500 p-6 rounded-lg text-center">
                                    <h3 class="text-xl font-semibold mb-3">Dokumen Rilis Pers</h3>
                                    <p class="text-gray-700 mb-4">
                                        Lihat atau unduh dokumen rilis pers resmi yang terlampir.
                                    </p>
                                    <a href="{{ $documentUrl }}"
                                        target="_blank"
                                        download
                                        class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download Dokumen
                                    </a>
                                </div>
                                @else
                                <div class="bg-red-100 border-l-4 border-red-500 p-6 rounded-lg text-center">
                                    <p class="text-red-700">Dokumen rilis pers tidak ditemukan.</p>
                                </div>
                                @endif

                                {{-- Tampilkan Meta Description sebagai Ringkasan --}}
                                @if(isset($post->seo_meta['description']) && $post->seo_meta['description'])
                                <div class="mt-6 prose max-w-none">
                                    <p class="text-lg text-gray-700 italic">
                                        "{{ $post->seo_meta['description'] }}"
                                    </p>
                                </div>
                                @endif

                                {{-- BARU: Logika untuk Kebijakan --}}
                                @elseif($post->type === 'kebijakan')
                                {{-- 1. Tampilkan Konten Artikel --}}
                                <div class="prose max-w-none">
                                    {!! $post->content !!}
                                </div>

                                {{-- 2. Tampilkan Tombol Download --}}
                                @php
                                $documentUrl = $post->getFirstMediaUrl('document');
                                @endphp
                                @if($documentUrl)
                                <div class="mt-8 pt-6 border-t border-gray-200"> {{-- Pemisah --}}
                                    <div class="bg-gray-100 border-l-4 border-indigo-500 p-6 rounded-lg text-center">
                                        <h3 class="text-xl font-semibold mb-3">Dokumen Kebijakan</h3>
                                        <p class="text-gray-700 mb-4">
                                            Lihat atau unduh dokumen kebijakan resmi yang terlampir.
                                        </p>
                                        <a href="{{ $documentUrl }}"
                                            target="_blank"
                                            download
                                            class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition-colors">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download Dokumen
                                        </a>
                                    </div>
                                </div>
                                @endif
                                {{-- Akhir Logika Kebijakan --}}

                                @endif
                            </div>
                            {{-- ======================================================== --}}
                            {{-- KONTEN DINAMIS BERDASARKAN TIPE (PERUBAHAN SELESAI DI SINI) --}}
                            {{-- ======================================================== --}}


                            {{-- FORM KOMENTAR --}}
                            <div class="mt-12 pt-8 border-t border-gray-200">
                                <h3 class="text-2xl font-bold font-serif mb-6">Leave a Comment</h3>
                                <form class="space-y-4">
                                    <div>
                                        <label for="comment_name" class="sr-only">Name</label>
                                        <input type="text" id="comment_name" placeholder="Your Name" class="w-full px-4 py-2 border rounded-md">
                                    </div>
                                    <div>
                                        <label for="comment_email" class="sr-only">Email</label>
                                        <input type="email" id="comment_email" placeholder="Your Email" class="w-full px-4 py-2 border rounded-md">
                                    </div>
                                    <div>
                                        <label for="comment_message" class="sr-only">Comment</label>
                                        <textarea id="comment_message" rows="5" placeholder="Your Comment" class="w-full px-4 py-2 border rounded-md"></textarea>
                                    </div>
                                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">Post Comment</button>
                                </form>
                            </div>

                            {{-- YOU MAY ALSO LIKE (Related Posts) --}}
                            @if($relatedPosts->isNotEmpty())
                            <div class="mt-12 pt-8 border-t border-gray-200">
                                <h3 class="text-2xl font-bold font-serif mb-6">You May Also Like</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($relatedPosts as $rPost)
                                    <a href="{{ route('news.show', $rPost) }}" class="block group bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                                        {{-- USE ACCESSOR --}}
                                        <img src="{{ $rPost->thumbnail_url }}" alt="{{ $rPost->title }}" class="w-full h-32 object-cover rounded-t-lg">
                                        <div class="p-4">
                                            <p class="text-sm font-semibold group-hover:text-indigo-600">{{ Str::limit($rPost->title, 60) }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $rPost->published_at->format('M d, Y') }}</p>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </article>
                </div>

                {{-- Kolom Kanan: Sidebar --}}
                <div class="w-full lg:w-1/3">
                    <x-news-sidebar :recentPosts="$recentPosts" :categories="$categories" :recommendedPosts="$recommendedPosts" />
                </div>

            </div>
        </div>
    </div>
</x-app-layout>