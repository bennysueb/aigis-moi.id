@if(isset($items) && $items->count() > 0)
<div class="bg-gray-50 py-12">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Popular News</h2>
            <p class="mt-2 text-lg leading-8 text-gray-600">Stay updated with our popular articles and news.</p>
        </div>

        <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
            @foreach ($items as $post)


            <div class="relative bg-white overflow-hidden shadow-lg rounded-lg flex flex-col hover:scale-105 transition-transform duration-300">

                {{-- USE ACCESSOR: Mendukung Drive & Lokal --}}
                <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="w-full h-56 object-cover">

                <div class="p-6 ">
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-sm text-gray-500 font-semibold"><time datetime="{{ $post->published_at->format('Y-m-d') }}">{{ $post->published_at->format('M d, Y') }}</time></p>

                        {{-- ===== BARU: Label Tipe Event ===== --}}
                        <div>
                            <a href="#">
                                <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full"> {{ $post->category->name ?? 'Uncategorized' }}</span>
                            </a>
                        </div>
                        {{-- =================================== --}}
                    </div>

                    <h3 class="mt-3 text-lg font-semibold leading-6 text-gray-900 group-hover:text-gray-600">
                        <a href="{{ route('news.show', $post->slug) }}">
                            <span class="absolute inset-0"></span>
                            {{ $post->title }}
                        </a>
                    </h3>
                </div>

                <div class="flex-grow"></div>

                <div class="px-6 pb-6">
                    <hr>
                </div>

                <div>
                    <div class="px-6 pb-4">
                        <p class="text-gray-600 text-sm line-clamp-3">
                            {!! Str::limit(strip_tags($post->content), 400, '...') !!}

                        </p>
                    </div>
                </div>

                <div class="px-6 pb-6 mt-4">
                    <a href="{{ route('news.show', $post->slug) }}" class="block w-full bg-secondary-dark text-white font-bold py-3 px-8 rounded-lg text-md hover:bg-gray-700 transition-colors duration-300 shadow-lg text-center">
                        Learn More &rarr;
                    </a>
                </div>
            </div>


            @endforeach
        </div>
    </div>
</div>
@endif