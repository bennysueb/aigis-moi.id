@if(isset($items) && $items->count() > 0)
<div class="bg-light py-16 sm:py-24">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-accent sm:text-4xl">Upcoming Events</h2>
            <p class="mt-2 text-lg leading-8 text-secondary-light">Join our exciting upcoming events.</p>
        </div>
        <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
            @foreach ($items as $event)
            <div class="relative bg-white overflow-hidden shadow-lg rounded-lg flex flex-col hover:scale-105 transition-transform duration-300">
                @if($event->hasMedia())
                <img src="{{ $event->getFirstMediaUrl('default', 'card-banner') }}" alt="{{ $event->name }}" class="w-full h-56 object-cover">
                @else
                <div class="w-full h-56 bg-primary"></div>
                @endif

                <div class="p-6 ">
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-sm text-gray-500 font-semibold">{{ $event->start_date->format('d M Y') }}</p>

                        {{-- ===== BARU: Label Tipe Event ===== --}}
                        <div>
                            @if($event->type === 'online')
                            <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Online</span>
                            @elseif($event->type === 'offline')
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Offline</span>
                            @elseif($event->type === 'hybrid')
                            <span class="inline-block bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Hybrid</span>
                            @endif
                        </div>
                        {{-- =================================== --}}
                    </div>

                    <h3 class="text-xl font-bold font-heading text-green-dark">{{ $event->name }}</h3>

                    {{-- ===== DIUBAH: Menampilkan Lokasi/Info Online secara dinamis ===== --}}
                    <p class="mt-2 text-base text-gray-500">
                        @if($event->type === 'offline' || $event->type === 'hybrid')
                        {{-- Mengambil data terjemahan venue --}}
                        {{ $event->getTranslation('venue', 'id') ?: $event->getTranslation('venue', 'en') }}
                        @else
                        The event was held online.
                        @endif
                    </p>
                    {{-- =============================================================== --}}
                </div>

                <div class="flex-grow"></div>

                <div class="px-6 pb-6">
                    <hr class="border-secondary-light">
                </div>

                <div>
                    <div class="px-6 pb-4">
                        <p class="text-gray-600 text-sm line-clamp-3">
                            {!! Str::limit(strip_tags($event->description), 400, '...') !!}
                        </p>
                    </div>
                </div>

                <div class="px-6 pb-6 mt-4">
                    <a href="{{ route('events.show', $event) }}" class="block w-full bg-secondary-light text-white font-bold py-3 px-8 rounded-lg text-md hover:bg-accent transition-colors duration-300 shadow-lg text-center">
                        Learn More &rarr;
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-20 text-center">
            <a href="{{ route('events.index') }}"
                class="inline-block bg-accent text-gray-900 font-semibold py-3 px-8 rounded-lg shadow-lg shadow-accent-500/20 hover:bg-accent-400 transition-all duration-300 transform hover:scale-105">
                Lihat Semua Events &rarr;
            </a>
        </div>
    </div>
</div>
@endif