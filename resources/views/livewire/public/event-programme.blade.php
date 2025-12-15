<div class="container mx-auto px-4 py-12">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-gray-900">Event Programme</h1>
        <p class="text-gray-600">Susunan acara dan program kegiatan mendatang.</p>
    </div>

    @if($groupedProgrammes->isEmpty())
    <div class="text-center py-10 bg-gray-50 rounded-lg">
        <p class="text-gray-500">Belum ada program acara yang dijadwalkan saat ini.</p>
    </div>
    @else
    <div class="max-w-4xl mx-auto">
        @foreach($groupedProgrammes as $date => $programmes)
        <div class="mb-12">
            <div class="sticky top-0 bg-white z-10 py-2 border-b-2 border-purple-500 mb-6">
                <h3 class="text-xl font-bold text-purple-600">
                    {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </h3>
            </div>

            <div class="space-y-6">
                @foreach($programmes as $prog)
                <div class="flex flex-col md:flex-row bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                    <div class="bg-purple-50 p-4 md:w-1/4 flex flex-col justify-center items-center text-center border-r border-purple-100">
                        <span class="text-lg font-bold text-purple-800">{{ $prog->start_time->format('H:i') }}</span>
                        <span class="text-sm text-gray-500">s/d</span>
                        <span class="text-lg font-bold text-purple-800">{{ $prog->end_time->format('H:i') }}</span>
                    </div>

                    <div class="p-5 md:w-3/4 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start">
                                <h4 class="text-lg font-bold text-gray-800 mb-1">{{ $prog->title }}</h4>
                                <div class="flex flex-wrap gap-1 justify-end">
                                    @if($prog->events->isEmpty())
                                    <span class="text-xs font-semibold bg-green-100 text-green-800 px-2 py-1 rounded">
                                        Umum / General
                                    </span>
                                    @else
                                    @foreach($prog->events as $evt)
                                    <span class="text-xs font-semibold bg-purple-100 text-purple-800 px-2 py-1 rounded text-right">
                                        {{ is_array($evt->name) ? $evt->name['id'] ?? $evt->name['en'] : $evt->name }}
                                    </span>
                                    @endforeach
                                    @endif
                                </div>
                            </div>

                            @if($prog->description)
                            <p class="text-gray-600 text-sm mb-3">{{ $prog->description }}</p>
                            @endif

                            <div class="flex flex-wrap gap-4 text-sm text-gray-500 mt-2">
                                @if($prog->location)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $prog->location }}
                                </div>
                                @endif

                                @if($prog->speaker)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $prog->speaker }}
                                </div>
                                @endif
                            </div>
                        </div>

                        @if($prog->link_url)
                        <div class="mt-4 pt-3 border-t border-gray-100 text-right">
                            <a href="{{ $prog->link_url }}" target="_blank" class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-800 transition-colors">
                                Lihat Detail
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>