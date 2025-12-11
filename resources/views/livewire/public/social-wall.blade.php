<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-48"
    x-data="{
        masonry: null,
        observer: null,
        initMasonry() {
            if (this.masonry) {
                this.masonry.destroy();
            }
            if (this.observer) {
                this.observer.disconnect();
            }

            const grid = this.$refs.grid;

            setTimeout(() => {
                imagesLoaded(grid, () => {
                    this.masonry = new Masonry(grid, {
                        itemSelector: '.grid-item',
                        percentPosition: true,
                        gutter: 16
                    });

                    // Create a single observer to watch all items
                    this.observer = new ResizeObserver(() => {
                        this.masonry.layout();
                    });

                    // Observe each grid item individually for size changes
                    grid.querySelectorAll('.grid-item').forEach(item => {
                        this.observer.observe(item);
                    });
                });
            }, 300); // A generous timeout for scripts to start loading
        }
    }"
    x-init="initMasonry()"
    @items-loaded.window="initMasonry()">

    <h1 class="text-4xl font-bold text-center mb-4">Social Wall</h1>
    <p class="text-center text-gray-600 mb-8">See what's happening in our community across social media.</p>

    <!-- Filter Buttons -->
    <div class="flex justify-center items-center flex-wrap gap-2 mb-12">
        <button wire:click="setFilter(null)"
            class="px-4 py-2 rounded-full text-sm font-semibold transition flex items-center 
                       {{ $filterType === null ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            All
        </button>
        @foreach ($socialMediaTypes as $type)
        <button wire:click="setFilter({{ $type->id }})"
            class="px-4 py-2 rounded-full text-sm font-semibold transition flex items-center 
                           {{ $filterType == $type->id ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            <span class="w-5 h-5 mr-2 text-center"><i class="{{ $type->icon_class }}"></i></span>
            {{ $type->name }}
        </button>
        @endforeach
    </div>

    <!-- Masonry Grid -->
    <div x-ref="grid" class="w-full">
        @foreach ($items as $item)
        <div class="grid-item w-full sm:w-1/2 md:w-1/3 lg:w-1/4 p-2">
            @if(Str::contains($item->embed_code, 'youtube.com'))
            <div class="aspect-w-16 aspect-h-9">
                {!! $item->embed_code !!}
            </div>
            @else
            {!! $item->embed_code !!}
            @endif
        </div>
        @endforeach
    </div>

    @if ($hasMorePages)
    <div x-data="{
             observe () {
                 let observer = new IntersectionObserver((entries) => {
                     entries.forEach(entry => {
                         if (entry.isIntersecting) {
                             @this.call('loadItems')
                         }
                     })
                 }, { rootMargin: '300px' })
                 observer.observe(this.$el)
             }
         }" x-init="observe()" class="text-center mt-8">
        <button wire:click="loadItems" class="px-6 py-3 bg-primary text-white rounded-md hover:bg-opacity-90">Loading More...</button>
    </div>
    @else
    <p class="text-center text-gray-500 mt-8">You've reached the end!</p>
    @endif

</div>