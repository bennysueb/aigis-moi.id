<x-app-layout>
    {{-- BAGIAN 1: HEADER UTAMA DENGAN BANNER DI ATAS --}}
    <div class="bg-white">
        <div class="max-w-7xl mx-auto pt-12 pb-8 px-4 sm:px-6 lg:px-8 text-center">
            {{-- BANNER KEMBALI DI SINI --}}
            @if($event->hasMedia())
            <div class="mb-12">
                <img src="{{ $event->getFirstMediaUrl('default', 'page-banner') }}" alt="{{ $event->name }}" class="w-full h-64 object-cover rounded-lg shadow-xl">
            </div>
            @endif

            <p class="text-base font-semibold text-indigo-600 uppercase tracking-wide">{{ $event->theme }}</p>
            <h1 class="mt-2 text-4xl font-extrabold font-heading text-gray-900 sm:text-5xl md:text-6xl">
                {{ $event->name }}
            </h1>
            <div class="mt-6 text-center mb-6">
                @if($event->type === 'offline')
                <span class="inline-block bg-blue-100 text-blue-800 text-sm font-semibold mr-2 px-2.5 py-1 rounded">Offline Event</span>
                @elseif($event->type === 'online')
                <span class="inline-block bg-green-100 text-green-800 text-sm font-semibold mr-2 px-2.5 py-1 rounded">Online Event</span>
                @else
                <span class="inline-block bg-blue-100 text-blue-800 text-sm font-semibold mr-2 px-2.5 py-1 rounded">Offline</span>
                <span class="inline-block bg-green-100 text-green-800 text-sm font-semibold mr-2 px-2.5 py-1 rounded">Online</span>
                @endif
            </div>
            <div class="mt-6 max-w-md mx-auto text-lg text-gray-500">
                <span>{{ $event->start_date->format('l, d F Y') }}</span>
                <span class="mx-2">&bull;</span>
                <span>{{ $event->venue }}</span>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: KONTEN UTAMA (Dua Kolom) --}}
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:space-x-12">

                {{-- KOLOM KIRI: FORM REGISTRASI (DIPERBESAR) --}}
                <div class="w-full lg:w-3/5">
                    <div class="bg-white rounded-lg shadow-lg p-6 lg:p-8">
                        <h3 class="text-2xl font-bold font-heading mb-4">Online Check-in</h3>
                        <p class="text-gray-600 mb-6">Masukkan email yang Anda gunakan saat mendaftar untuk mengkonfirmasi kehadiran Anda.</p>

                        @if(session('error'))
                        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('online.checkin.store', $event) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" name="email" id="email" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        Confirm Attendance
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- KOLOM KANAN: Deskripsi, Narasumber, Moderator --}}
                <div class="w-full lg:w-2/5 mt-12 lg:mt-0">
                    <div class="prose max-w-none">
                        {!! $event->description !!}
                    </div>

                    {{-- NARASUMBER & MODERATOR --}}
                    @if(!empty($event->personnel['speakers']) || !empty($event->personnel['moderators']))
                    <div class="mt-12">
                        <h3 class="text-2xl font-bold font-heading mb-6 border-t pt-8">Speakers & Moderators</h3>
                        <div class="space-y-8">
                            @foreach($event->personnel['speakers'] ?? [] as $person)
                            <div class="flex items-start space-x-4">
                                <img src="{{ $person['photo_url'] ?? '/img/placeholder.png' }}" class="w-20 h-20 rounded-full object-cover flex-shrink-0">
                                <div>
                                    <div class="font-bold">{{ $person['name'] }}</div>
                                    <div class="text-sm text-indigo-600 font-semibold">Speaker</div>
                                    <div class="text-sm text-gray-600">{{ $person['organization'] }}</div>
                                </div>
                            </div>
                            @endforeach
                            @foreach($event->personnel['moderators'] ?? [] as $person)
                            <div class="flex items-start space-x-4">
                                <img src="{{ $person['photo_url'] ?? '/img/placeholder.png' }}" class="w-20 h-20 rounded-full object-cover flex-shrink-0">
                                <div>
                                    <div class="font-bold">{{ $person['name'] }}</div>
                                    <div class="text-sm text-indigo-600 font-semibold">Moderator</div>
                                    <div class="text-sm text-gray-600">{{ $person['organization'] }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- BAGIAN 3: SPONSOR --}}
            @if(!empty($event->sponsors))
            <div class="mt-20">
                <h3 class="text-3xl font-bold font-heading mb-8 text-center text-gray-800">Our Sponsors</h3>
                @foreach(['platinum', 'gold', 'silver'] as $tier)
                @if(!empty($event->sponsors[$tier]))
                <div class="mb-10">
                    <h4 class="font-semibold text-lg uppercase text-gray-500 text-center mb-6 tracking-widest">{{ $tier }}</h4>
                    <div class="flex justify-center items-center flex-wrap gap-x-12 gap-y-8">
                        @foreach($event->sponsors[$tier] as $sponsor)
                        <a href="{{ $sponsor['link'] }}" target="_blank" class="block">
                            <img src="{{ $sponsor['logo_url'] }}" alt="{{ $sponsor['name'] }}" class="h-12 md:h-16 grayscale hover:grayscale-0 transition-all duration-300">
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>
</x-app-layout>