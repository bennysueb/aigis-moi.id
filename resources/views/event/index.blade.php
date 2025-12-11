<x-app-layout>
    <div class="w-full">
        {{-- ====================================================== --}}
        {{-- =========== BAGIAN HEADER 3-KOLOM BARU =========== --}}
        {{-- ====================================================== --}}
        <div class="bg-gray-100 py-8">
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-12 gap-8 items-center">

                    {{-- === KOLOM IKLAN KIRI === --}}
                    <div class="hidden lg:block col-span-2 h-96">
                        @if ($leftAd && $leftAd->hasMedia())
                        <a href="{{ $leftAd->url ?? '#' }}" target="_blank" class="block w-full h-full rounded-lg overflow-hidden group relative">
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
                        @if($bannerEvents->count() > 0)
                        <div x-data="{ 
                                    activeSlide: 1, 
                                    totalSlides: {{ $bannerEvents->count() }},
                                    autoplay: null,
                                    startAutoplay() {
                                        this.autoplay = setInterval(() => {
                                            this.activeSlide = this.activeSlide === this.totalSlides ? 1 : this.activeSlide + 1;
                                        }, 5000);
                                    },
                                    stopAutoplay() {
                                        clearInterval(this.autoplay);
                                    }
                                }"
                            x-init="startAutoplay()"
                            @mouseenter="stopAutoplay()"
                            @mouseleave="startAutoplay()"
                            class="relative rounded-lg shadow-xl h-96 w-full overflow-hidden">

                            @foreach($bannerEvents as $event)
                            <div x-show="activeSlide === {{ $loop->iteration }}"
                                x-transition:enter="transition-opacity ease-in-out duration-1000"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition-opacity ease-in-out duration-1000"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="absolute inset-0 w-full h-full">
                                <img src="{{ $event->getFirstMediaUrl('default', 'page-banner') }}" alt="{{ $event->name }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-green-900 via-green-800/70 to-transparent opacity-90"></div>
                                <div class="absolute inset-0 z-10 p-8 sm:p-12 flex flex-col justify-end text-white">
                                    @if($event->theme)
                                    <p class="text-green-300 font-semibold text-lg mb-1">{{ $event->theme }}</p>
                                    @endif
                                    <h2 class="text-3xl md:text-5xl font-bold font-heading mb-3">
                                        {{ $event->name }}
                                    </h2>
                                    <p class="text-base md:text-lg text-green-100 max-w-3xl mb-5">
                                        <span class="font-semibold">{{ $event->start_date->locale(app()->getLocale())->translatedFormat('l, d F Y') }}</span>
                                        <span class="mx-2">|</span>
                                        <span>
                                            @if($event->type === 'hybrid' || $event->type === 'offline')
                                            {{ $event->venue }}
                                            @endif

                                            @if($event->type === 'hybrid' || $event->type === 'online')
                                            {{ $event->platform ?? 'Online' }}
                                            @endif
                                        </span>
                                        @if($event->quota > 0)
                                        <span class="mx-2">|</span>
                                        <span>
                                            Kuota Tersisa: {{ $event->quota - $event->registrations_count }}
                                        </span>
                                        @endif
                                    </p>
                                    <a href="{{ route('events.show', $event) }}" class="inline-block w-auto bg-green-600 text-white font-bold py-3 px-6 rounded transition-colors duration-300 hover:bg-green-700 text-center" style="max-width: 200px;">
                                        {{ __('button.register_now') }}
                                    </a>
                                </div>
                            </div>
                            @endforeach

                            {{-- Slider Controls --}}
                            <button x-on:click="activeSlide = activeSlide === 1 ? totalSlides : activeSlide - 1" class="absolute z-20 top-1/2 left-4 -translate-y-1/2 bg-white/30 text-white rounded-full p-2 backdrop-blur-sm transition hover:bg-white/50">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                </svg>
                            </button>
                            <button x-on:click="activeSlide = activeSlide === totalSlides ? 1 : activeSlide + 1" class="absolute z-20 top-1/2 right-4 -translate-y-1/2 bg-white/30 text-white rounded-full p-2 backdrop-blur-sm transition hover:bg-white/50">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                            <div class="absolute z-20 bottom-6 left-1/2 -translate-x-1/2 flex space-x-2">
                                @foreach($bannerEvents as $event)
                                <button x-on:click="activeSlide = {{ $loop->iteration }}" :class="{ 'bg-white': activeSlide === {{ $loop->iteration }}, 'bg-white/40': activeSlide !== {{ $loop->iteration }} }" class="w-3 h-3 rounded-full transition-all"></button>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- === KOLOM IKLAN KANAN === --}}
                    <div class="hidden lg:block col-span-2 h-96">
                        @if ($rightAd && $rightAd->hasMedia())
                        <a href="{{ $rightAd->url ?? '#' }}" target="_blank" class="block w-full h-full rounded-lg overflow-hidden group relative">
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


            {{-- ====================================================== --}}
            {{-- ============ BAGIAN IKLAN BAWAH ============= --}}
            {{-- ====================================================== --}}
            @if ($bottomAd && $bottomAd->hasMedia())
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
                <a href="{{ $bottomAd->url ?? '#' }}" target="_blank" class="block w-full rounded-lg overflow-hidden group relative">
                    <img src="{{ $bottomAd->getFirstMediaUrl('default', 'ad-wide') }}" alt="{{ $bottomAd->headline }}" class="w-full h-auto object-contain transition-transform duration-300 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
            </div>
            @endif

            {{-- ====================================================== --}}
            {{-- =========== FILTER & VIEW SWITCHER SECTION =========== --}}
            {{-- ====================================================== --}}
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 mb-6">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col lg:flex-row gap-4 justify-between items-center">

                    {{-- Form Filter --}}
                    <form action="{{ route('events.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto items-center">
                        {{-- Pertahankan view type saat filtering --}}
                        <input type="hidden" name="view" value="{{ $viewType }}">

                        {{-- Dropdown Bulan --}}
                        <select name="month" class="w-full sm:w-40 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <option value="">All Months</option>
                            @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ (request('month') == $m || (isset($selectedMonth) && $selectedMonth == $m)) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                            @endforeach
                        </select>

                        {{-- Dropdown Tahun --}}
                        <select name="year" class="w-full sm:w-32 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <option value="">All Years</option>
                            @foreach(range(2024, 2026) as $y)
                            <option value="{{ $y }}" {{ (request('year') == $y || (isset($selectedYear) && $selectedYear == $y)) ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                            @endforeach
                        </select>

                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm w-full sm:w-auto flex justify-center items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search
                            </button>

                            {{-- Tombol Reset --}}
                            @if(request('month') || request('year'))
                            <a href="{{ route('events.index', ['view' => $viewType]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-2 px-4 rounded-lg transition-colors text-sm w-full sm:w-auto text-center">
                                Reset
                            </a>
                            @endif
                        </div>
                    </form>

                    {{-- Switch View Buttons --}}
                    <div class="flex bg-gray-100 p-1 rounded-lg border border-gray-200">
                        <a href="{{ route('events.index', array_merge(request()->query(), ['view' => 'list'])) }}"
                            class="px-4 py-2 rounded-md text-sm font-medium transition-all {{ $viewType === 'list' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                                List
                            </div>
                        </a>
                        <a href="{{ route('events.index', array_merge(request()->query(), ['view' => 'calendar'])) }}"
                            class="px-4 py-2 rounded-md text-sm font-medium transition-all {{ $viewType === 'calendar' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Calendar
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ====================================================== --}}
            {{-- ================= KONTEN UTAMA ======================= --}}
            {{-- ====================================================== --}}

            @if($viewType === 'calendar')
            {{-- === TAMPILAN KALENDER (TABLE BASED - PIXEL PERFECT) === --}}
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">

                    {{-- Header Kalender & Navigasi --}}
                    <div class="p-6 bg-white border-b border-gray-200 flex items-center justify-between">
                        {{-- Tombol Previous --}}
                        <a href="{{ route('events.index', ['view' => 'calendar', 'month' => $prevDate->month, 'year' => $prevDate->year]) }}"
                            class="p-2 rounded-full hover:bg-gray-100 text-gray-600 transition group" title="Previous Month">
                            <svg class="w-6 h-6 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>

                        <h2 class="text-2xl font-bold text-gray-800 text-center flex items-center gap-2">
                            <span class="text-indigo-600">{{ $calendarDate->format('F') }}</span>
                            <span class="text-gray-400 font-light">{{ $calendarDate->format('Y') }}</span>
                        </h2>

                        {{-- Tombol Next --}}
                        <a href="{{ route('events.index', ['view' => 'calendar', 'month' => $nextDate->month, 'year' => $nextDate->year]) }}"
                            class="p-2 rounded-full hover:bg-gray-100 text-gray-600 transition group" title="Next Month">
                            <svg class="w-6 h-6 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    {{-- Wrapper untuk Scroll Horizontal di Mobile --}}
                    <div class="overflow-x-auto">
                        <div class="min-w-[800px]">

                            {{-- MENGGUNAKAN HTML TABLE UNTUK ALIGNMENT SEMPURNA --}}
                            <table class="w-full border-collapse table-fixed">
                                <thead>
                                    <tr>
                                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                                        <th class="w-[14.28%] border-b border-r border-gray-200 bg-gray-50 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider last:border-r-0 sticky top-0 z-10 shadow-sm">
                                            {{ $day }}
                                        </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @php
                                    $daysInMonth = $calendarDate->daysInMonth;
                                    $startDayOfWeek = $calendarDate->copy()->startOfMonth()->dayOfWeek;

                                    // Total sel yang dibutuhkan (kosong awal + hari + sisa untuk genapkan baris)
                                    $totalSlots = $startDayOfWeek + $daysInMonth;
                                    $rows = ceil($totalSlots / 7);

                                    $currentDayCounter = 1;
                                    @endphp

                                    @for ($row = 0; $row < $rows; $row++)
                                        <tr class="border-b border-gray-200 last:border-b-0">
                                        @for ($col = 0; $col < 7; $col++)
                                            @php
                                            $cellIndex=($row * 7) + $col;
                                            // Cek apakah sel ini adalah tanggal valid
                                            $isDate=$cellIndex>= $startDayOfWeek && $currentDayCounter <= $daysInMonth;
                                                @endphp

                                                <td class="border-r border-gray-200 last:border-r-0 p-0 align-top h-32 md:h-40 hover:bg-indigo-50/30 transition relative group">
                                                @if ($isDate)
                                                @php
                                                $currentDate = $calendarDate->copy()->day($currentDayCounter);
                                                $isToday = $currentDate->isToday();

                                                // Filter events untuk tanggal ini
                                                $dayEvents = $calendarEvents->filter(function($event) use ($currentDate) {
                                                return $currentDate->between($event->start_date->startOfDay(), $event->end_date->endOfDay());
                                                });
                                                @endphp

                                                <div class="w-full h-full p-2 flex flex-col">
                                                    {{-- Angka Tanggal --}}
                                                    <div class="flex justify-between items-start mb-1">
                                                        <span class="text-sm font-semibold w-7 h-7 flex items-center justify-center rounded-full {{ $isToday ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-700' }}">
                                                            {{ $currentDayCounter }}
                                                        </span>
                                                        @if($dayEvents->count() > 0)
                                                        <span class="text-[10px] text-gray-400 font-medium hidden sm:inline-block">{{ $dayEvents->count() }} Events</span>
                                                        @endif
                                                    </div>

                                                    {{-- List Event --}}
                                                    <div class="flex flex-col gap-1.5 overflow-y-auto flex-grow custom-scrollbar max-h-[inherit]">
                                                        @foreach($dayEvents as $event)
                                                        <a href="{{ route('events.show', $event) }}" class="group/item relative block">
                                                            <div class="text-xs p-1.5 rounded bg-indigo-50 text-indigo-700 hover:bg-indigo-100 hover:shadow-sm border-l-2 border-indigo-500 transition-all truncate">
                                                                {{ $event->name }}
                                                            </div>
                                                        </a>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                @php $currentDayCounter++; @endphp
                                                @else
                                                {{-- Kotak Kosong --}}
                                                <div class="w-full h-full bg-gray-50/30"></div>
                                                @endif
                                                </td>
                                                @endfor
                                                </tr>
                                                @endfor
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div class="bg-gray-50 p-3 text-xs text-gray-400 text-center border-t border-gray-200">
                        Showing events for {{ $calendarDate->format('F Y') }}
                    </div>
                </div>
            </div>

            @else
            {{-- === TAMPILAN LIST (DEFAULT) === --}}
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- UPCOMING EVENTS --}}
                <div class="mb-12">
                    <h1 class="text-4xl font-bold font-heading mb-8 flex items-center gap-3">
                        <span class="bg-indigo-600 w-2 h-10 rounded-full"></span>
                        {{ __('events.upcoming_events') }}
                    </h1>

                    @if($upcomingEvents->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($upcomingEvents as $event)
                        <div class="bg-white overflow-hidden sm:rounded-lg flex flex-col border border-gray-200 transition-all duration-300 ease-in-out hover:shadow-xl hover:-translate-y-1">
                            <div class="relative">
                                <img src="{{ $event->getFirstMediaUrl('default', 'card-banner') }}" alt="{{ $event->name }}" class="w-full h-48 object-cover">
                                {{-- Label Status (Paid/Free) --}}
                                <div class="absolute top-4 left-4">
                                    @if($event->is_paid_event)
                                    <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                                        {{ __('dashboard.Paid') }}
                                    </span>
                                    @else
                                    <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                                        {{ __('dashboard.Free') }}
                                    </span>
                                    @endif
                                </div>
                                {{-- Badge Tanggal --}}
                                <div class="absolute top-4 right-4 bg-white backdrop-blur rounded-lg px-3 py-1 shadow-sm text-center">
                                    <div class="text-xs text-gray-500 font-bold uppercase">{{ $event->start_date->format('M') }}</div>
                                    <div class="text-xl text-indigo-600 font-bold">{{ $event->start_date->format('d') }}</div>
                                </div>
                            </div>

                            <div class="p-6 text-gray-900 flex-grow">
                                @if($event->theme)
                                <p class="text-indigo-600 font-semibold text-xs uppercase tracking-wide mb-1">{{ $event->theme }}</p>
                                @endif
                                <h2 class="text-xl font-bold font-heading mb-3 line-clamp-2 hover:text-indigo-600 transition">{{ $event->name }}</h2>

                                <div class="mt-4 text-sm text-gray-500 space-y-2">
                                    {{-- Tanggal --}}
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>
                                            {{ $event->start_date->format('d F Y') }}
                                            @if($event->start_date->format('H:i') !== '00:00')
                                            | {{ $event->start_date->format('H:i') }} WIB
                                            @endif
                                        </span>
                                    </div>

                                    {{-- Lokasi / Platform --}}
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div class="flex flex-col">
                                            @if($event->type === 'online')
                                            <span>Online via {{ $event->platform === 'Lainnya...' && isset($event->meeting_info['platform_name']) ? $event->meeting_info['platform_name'] : ($event->platform ?? 'Platform') }}</span>
                                            @elseif($event->type === 'hybrid')
                                            <span>{{ is_array($event->venue) ? ($event->venue['name'] ?? $event->venue) : $event->venue }}</span>
                                            <span class="text-xs text-indigo-500 font-semibold">+ Online Access</span>
                                            @else
                                            <span>{{ is_array($event->venue) ? ($event->venue['name'] ?? $event->venue) : $event->venue }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-6 pb-6 pt-0">
                                <a href="{{ route('events.show', $event) }}" class="block w-full text-center bg-green-600 text-white font-bold py-2 px-4 rounded transition-colors duration-300 hover:bg-green-700">
                                    {{ __('button.register_now') }}
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-gray-50 rounded-lg p-12 text-center border-2 border-dashed border-gray-200">
                        <p class="text-gray-500 text-lg">{{ __('events.no_upcoming_events') }}</p>
                        @if(request('month') || request('year'))
                        <a href="{{ route('events.index') }}" class="text-indigo-600 hover:underline mt-2 inline-block">Clear Filters</a>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- PAST EVENTS --}}
                <div class="mb-12 pt-8 border-t border-gray-200">
                    <h1 class="text-3xl font-bold font-heading mb-8 text-gray-500 flex items-center gap-3">
                        <span class="bg-gray-300 w-2 h-8 rounded-full"></span>
                        {{ __('events.past_events') }}
                    </h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 opacity-80 hover:opacity-100 transition-opacity duration-300">
                        @forelse($pastEvents as $event)
                        <div class="bg-white overflow-hidden sm:rounded-lg flex flex-col border border-gray-200 transition-all duration-300 ease-in-out hover:shadow-lg">
                            <div class="relative">
                                <img src="{{ $event->getFirstMediaUrl('default', 'card-banner') }}" alt="{{ $event->name }}" class="w-full h-48 object-cover transition-all duration-500">
                                <div class="absolute inset-0 bg-gray-900/10"></div>
                            </div>

                            <div class="p-6 text-gray-900 flex-grow">
                                @if($event->theme)
                                <p class="text-gray-400 font-semibold text-xs mb-1">{{ $event->theme }}</p>
                                @endif
                                <h2 class="text-xl font-bold font-heading mb-2 text-gray-700">{{ $event->name }}</h2>
                                <p class="text-sm text-gray-500 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $event->start_date->format('d M Y') }}
                                </p>
                            </div>
                            <div class="px-6 pb-6">
                                <a href="{{ route('events.show', $event) }}" class="inline-block bg-transparent border border-gray-300 text-gray-700 font-bold py-2 px-4 rounded transition-colors duration-300 hover:bg-gray-100 hover:border-gray-400">
                                    {{ __('button.view_archive') }}
                                </a>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 col-span-full italic">There are no past events to show.</p>
                        @endforelse
                    </div>
                </div>

            </div>
            @endif
        </div>
    </div>
</x-app-layout>