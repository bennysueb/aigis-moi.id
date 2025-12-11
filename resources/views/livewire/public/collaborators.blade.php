<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">
                Our Collaborators
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                We are proud to work with these amazing companies and organizations.
            </p>
        </div>

        <div class="space-y-16">
            @forelse($categories as $category)
            @if($category->collaborators->isNotEmpty())
            <div class="relative">

                <div class="text-center mb-8 relative">
                    <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-wider inline-block bg-gray-50 px-4 relative z-10">
                        {{ $category->name }}
                    </h2>
                    <div class="absolute inset-0 flex items-center justify-center -z-0">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                </div>

                <div class="flex flex-wrap justify-center gap-6 md:gap-8">

                    @foreach($category->collaborators as $col)
                    <div class="group relative flex items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100
                                    {{-- ATUR UKURAN ITEM AGAR RAPI --}}
                                    {{ $category->type === 'sponsor' 
                                        ? 'w-full sm:w-72 h-40 md:h-48'  /* Sponsor: Kotak Besar */
                                        : 'w-full sm:w-72 h-40 md:h-48'         /* Partner: Kotak Kecil */
                                    }}">

                        @if($col->url_link)
                        <a href="{{ $col->url_link }}" target="_blank" rel="noopener noreferrer" class="absolute inset-0 z-10"></a>
                        @endif

                        <img src="{{ $col->logo_url }}"
                            alt="{{ $col->name }}"
                            class="object-contain w-full h-full transition-transform duration-300 group-hover:scale-105 p-2
                                                {{ $category->type === 'sponsor' ? '' : 'grayscale group-hover:grayscale-0 opacity-80 group-hover:opacity-100' }}">

                        <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-20 shadow-lg">
                            {{ $col->name }}
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                        </div>

                    </div>
                    @endforeach

                </div>
            </div>
            @endif
            @empty
            <div class="text-center py-20">
                <p class="text-gray-400">No collaborators found.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-20 text-center border-t pt-10 border-gray-200">
            <p class="text-gray-600">Interested in becoming a partner?</p>
            <a href="/contact" class="text-indigo-600 font-semibold hover:underline mt-2 inline-block">
                Contact Us &rarr;
            </a>
        </div>

    </div>
</div>