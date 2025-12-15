@props(['recentPosts', 'categories', 'recommendedPosts'])

<div class="space-y-8">
    
    {{-- Recent Posts --}}
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h3 class="text-xl font-bold font-serif mb-4 border-b pb-2">Recent Posts</h3>
        <div class="space-y-4">
            @forelse($recentPosts as $rPost)
            <a href="{{ route('news.show', $rPost) }}" class="flex items-center group">
                {{-- USE ACCESSOR (HYBRID) --}}
                <img src="{{ $rPost->thumbnail_url }}" 
                     alt="{{ $rPost->title }}" 
                     class="w-16 h-16 object-cover rounded-md flex-shrink-0 bg-gray-100">
                
                <div class="ml-3">
                    <p class="text-sm font-semibold group-hover:text-indigo-600 line-clamp-2">
                        {{ Str::limit($rPost->title, 40) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $rPost->published_at->format('M d, Y') }}
                    </p>
                </div>
            </a>
            @empty
            <p class="text-sm text-gray-500 text-center py-4">No recent posts.</p>
            @endforelse
        </div>
    </div>

    {{-- Categories --}}
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h3 class="text-xl font-bold font-serif mb-4 border-b pb-2">Kategori</h3>
        <ul class="space-y-2">
            @foreach($categories as $category)
            @php
                // Jumlahkan post induk dan semua anaknya
                $totalPosts = $category->posts_count + $category->children->sum('posts_count');
            @endphp
            <li>
                <a href="{{ route('news.index', ['category' => $category->slug]) }}" class="flex justify-between items-center group p-2 hover:bg-gray-50 rounded transition">
                    <span class="font-semibold text-gray-700 group-hover:text-indigo-600">{{ $category->name }}</span>
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">{{ $totalPosts }}</span>
                </a>

                {{-- Tampilkan sub-kategori (children) --}}
                @if($category->children->isNotEmpty())
                <ul class="pl-4 mt-1 space-y-1 border-l-2 border-gray-100 ml-2">
                    @foreach($category->children as $child)
                    <li>
                        <a href="{{ route('news.index', ['category' => $child->slug]) }}" class="flex justify-between items-center group py-1 px-2 hover:bg-gray-50 rounded">
                            <span class="text-sm text-gray-600 group-hover:text-indigo-600">{{ $child->name }}</span>
                            <span class="text-xs text-gray-400">{{ $child->posts_count }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </li>
            @endforeach
        </ul>
    </div>

    {{-- Recommended Posts --}}
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h3 class="text-xl font-bold font-serif mb-4 border-b pb-2">Recommended Posts</h3>
        <div class="space-y-6">
            @forelse($recommendedPosts as $recPost)
            <a href="{{ route('news.show', $recPost) }}" class="block group">
                <div class="overflow-hidden rounded-md relative">
                    {{-- USE ACCESSOR (HYBRID) --}}
                    <img src="{{ $recPost->thumbnail_url }}" 
                         alt="{{ $recPost->title }}" 
                         class="w-full h-40 object-cover transform group-hover:scale-105 transition-transform duration-500">
                    
                    {{-- Category Badge --}}
                    <span class="absolute bottom-2 left-2 bg-indigo-600 text-white text-[10px] px-2 py-1 rounded uppercase font-bold tracking-wide">
                        {{ $recPost->categories->first()->name ?? 'News' }}
                    </span>
                </div>
                <p class="mt-3 text-sm font-bold text-gray-800 group-hover:text-indigo-600 leading-snug transition-colors">
                    {{ Str::limit($recPost->title, 60) }}
                </p>
                <p class="text-xs text-gray-500 mt-1">{{ $recPost->published_at->format('M d, Y') }}</p>
            </a>
            @empty
            <p class="text-sm text-gray-500 text-center py-4">No recommended posts.</p>
            @endforelse
        </div>
    </div>

    {{-- Social Media Share --}}
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h3 class="text-xl font-bold font-serif mb-4 border-b pb-2">Share This</h3>
        <div class="flex space-x-2">
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="flex-1 bg-blue-600 text-white flex items-center justify-center p-3 rounded-md hover:bg-blue-700 transition-colors">
                <i class="fab fa-facebook-f mr-2"></i> Facebook
            </a>
            <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}" target="_blank" class="flex-1 bg-blue-400 text-white flex items-center justify-center p-3 rounded-md hover:bg-blue-500 transition-colors">
                <i class="fab fa-twitter mr-2"></i> Twitter
            </a>
        </div>
    </div>

    {{-- Newsletter --}}
    <div class="p-6 bg-indigo-50 rounded-lg shadow-inner text-center border border-indigo-100">
        <h3 class="text-xl font-bold font-serif mb-2 text-indigo-900">Subscribe for Updates</h3>
        <p class="text-sm text-indigo-700 mb-4">Don't miss out on the latest news and insights.</p>
        <form onsubmit="return false;">
            <input type="email" placeholder="Your email address" class="w-full px-4 py-2 border border-indigo-200 rounded-md mb-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 text-sm font-semibold transition-colors shadow-sm">Subscribe</button>
        </form>
    </div>
</div>