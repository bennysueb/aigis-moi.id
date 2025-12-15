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
                                                @php
                                                    $sliderImg = $featuredPost->featured_image_drive_id 
                                                        ? route('media.stream.public', ['path' => $featuredPost->featured_image_drive_id]) 
                                                        : $featuredPost->getFirstMediaUrl('featured', 'page-banner');
                                                @endphp
                                                
                                                <img src="{{ $sliderImg ?: 'https://via.placeholder.com/800x400' }}" 
                                                     class="w-full h-96 object-cover opacity-50 group-hover:opacity-75 transition-opacity duration-300">
                                                     
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
                            <div class="swiper-pagination absolute bottom-4 left-0 right-0 z-10"></div>
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">



                {{-- ================================================= --}}
                {{-- BAGIAN 1: TAMPILAN JIKA DIFILTER (Sudah benar) --}}
                {{-- ================================================= --}}
                @if(isset($currentCategory))
                <section class="mb-12">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h1 class="text-3xl font-bold font-serif mb-2">
                                Kategori: {{ $currentCategory->getTranslation('name', $locale) }}
                            </h1>
                            <p class="text-lg text-gray-600">
                                Menampilkan semua post dalam kategori ini.
                            </p>
                        </div>
                        {{-- Tombol "Lihat Berita Seluruhnya" --}}
                        <a href="{{ route('news.index') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
                            Lihat Berita Seluruhnya &rarr;
                        </a>
                    </div>
                </section>

                <section class="mb-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @forelse($filteredPosts as $post)
                        <a href="{{ route('news.show', $post) }}" class="block group bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                            {{-- USE ACCESSOR --}}
                            <img src="{{ $post->thumbnail_url }}" class="w-full h-48 object-cover rounded-t-lg">
                            <div class="p-6">
                                <div class="flex flex-wrap gap-1 mb-2">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $currentCategory->getTranslation('name', $locale) }}
                                    </span>
                                </div>
                                <h3 class="mt-2 text-xl font-bold font-serif">{{ $post->getTranslation('title', $locale) }}</h3>
                            </div>
                        </a>
                        @empty
                        <div class="md-col-span-3 text-center py-12">
                            <p class="text-xl text-gray-500">Tidak ada post yang ditemukan dalam kategori ini.</p>
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        {{ $filteredPosts->links() }}
                    </div>
                </section>


                {{-- ================================================= --}}
                {{-- BAGIAN 2: TAMPILAN DEFAULT (JIKA TIDAK DIFILTER)  --}}
                {{-- ================================================= --}}
                @else

                <section class="mb-12">
                    <h2 class="text-3xl font-bold font-serif mb-6">Latest News</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($latestPosts as $post)
                        <a href="{{ route('news.show', $post) }}" class="block group bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300">
                            {{-- USE ACCESSOR --}}
                            <img src="{{ $post->thumbnail_url }}" class="w-full h-48 object-cover rounded-t-lg">
                            <div class="p-6">
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($post->categories as $category)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $category->name }}
                                    </span>
                                    @endforeach
                                </div>
                                <h3 class="mt-2 text-xl font-bold font-serif">{{ $post->title }}</h3>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </section>

                @if($categoriesWithPosts)
                @foreach($categoriesWithPosts as $category)
                @php
                // GABUNGKAN post dari induk DAN semua anaknya
                $allPosts = $category->posts
                ->merge($category->children->flatMap->posts)
                ->sortByDesc('published_at')
                ->take(4); // Ambil 4 terbaru dari gabungan
                @endphp

                {{-- Hanya tampilkan section jika ada postingan --}}
                @if($allPosts->isNotEmpty())
                <section class="mb-12">
                    {{-- Judul Kategori --}}
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h2 class="text-3xl font-bold font-serif text-secondary-dark">
                            {{ $category->getTranslation('name', $locale) }}
                        </h2>
                        <a href="{{ route('news.index', ['category' => $category->slug]) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold transition-colors duration-300">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    {{-- Grid Postingan --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        @foreach($allPosts as $post) {{-- Gunakan $allPosts --}}
                        <a href="{{ route('news.show', $post) }}" class="block group">
                            {{-- USE ACCESSOR --}}
                            <img src="{{ $post->thumbnail_url }}" class="w-full h-40 object-cover rounded-lg shadow-md">
                            <h3 class="mt-3 text-base font-bold group-hover:text-primary">
                                {{ $post->getTranslation('title', $locale) }}
                            </h3>
                        </a>
                        @endforeach
                    </div>
                </section>
                @endif
                @endforeach
                @endif

                @endif

            </div>
        </div>
    </div>
</x-app-layout>