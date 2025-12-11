<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-100">

        <div class="w-full sm:max-w-2xl mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">

            {{-- Header Event --}}
            <div class="text-center mb-8 border-b pb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Konfirmasi Kehadiran</h2>
                <p class="text-gray-600">Anda diundang untuk menghadiri:</p>
                <h1 class="text-xl text-blue-600 font-bold mt-1">{{ $event->name }}</h1>

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

                <div class="mt-4 text-sm text-gray-500 space-y-1">
                    @if($event->start_date->isSameDay($event->end_date))
                    <p class="text-gray-500"> 📅 {{ $event->start_date->locale(app()->getLocale())->translatedFormat('l, d F Y') }} | {{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }} WIB</p>
                    @else

                    @if($event->start_date->isSameMonth($event->end_date))

                    <p class="text-gray-500"> 📅 {{ $event->start_date->format('d') }} - {{ $event->end_date->locale(app()->getLocale())->translatedFormat('d F Y') }}</p>
                    @else

                    <p> 📅 {{ $event->start_date->locale(app()->getLocale())->translatedFormat('d F') }} - {{ $event->end_date->locale(app()->getLocale())->translatedFormat('d F Y') }}</p>
                    @endif
                    @endif

                    <div class="mt-4 text-sm text-gray-500 space-y-1">
                        {{-- Lokasi / Platform (Dinamis) --}}
                        @if($event->type === 'online')
                        {{-- KONDISI 1: ONLINE --}}
                        <p>💻 Online via {{ $event->platform === 'Lainnya...' && isset($event->meeting_info['platform_name']) ? $event->meeting_info['platform_name'] : ($event->platform ?? 'Online Platform') }}</p>

                        @elseif($event->type === 'hybrid')
                        {{-- KONDISI 2: HYBRID (Tampilkan Keduanya) --}}
                        <p>📍 {{ is_array($event->venue) ? ($event->venue['name'] ?? $event->venue) : $event->venue }}</p>
                        <p>💻 Online via {{ $event->platform === 'Lainnya...' && isset($event->meeting_info['platform_name']) ? $event->meeting_info['platform_name'] : ($event->platform ?? 'Online') }}</p>

                        @else
                        {{-- KONDISI 3: OFFLINE (Default) --}}
                        <p>📍 {{ is_array($event->venue) ? ($event->venue['name'] ?? $event->venue) : $event->venue }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pesan Error Session --}}
            @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p>{{ session('error') }}</p>
            </div>
            @endif

            {{-- ▼▼▼ LOGIKA PENGECEKAN STATUS EVENT ▼▼▼ --}}

            @if (!$event->is_active)
            {{-- KONDISI 1: EVENT TIDAK AKTIF --}}
            <div class="bg-red-50 border-l-4 border-red-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Event Tidak Aktif</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>Mohon maaf, event ini sedang dinonaktifkan atau ditutup sementara oleh panitia.</p>
                        </div>
                    </div>
                </div>
            </div>

            @elseif ($event->end_date < now())
                {{-- KONDISI 2: EVENT SUDAH BERAKHIR (TAMPILAN RECORDINGS / CONCLUDED) --}}

                @if (!empty($event->youtube_recordings))
                {{-- JIKA ADA REKAMAN --}}
                <h3 class="text-2xl font-bold font-heading mb-6 text-center">Event Recordings</h3>
                <div class="space-y-8">
                    @foreach($event->youtube_recordings as $recording)
                    @if(!empty($recording['link']))
                    @php
                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $recording['link'], $match);
                    $videoId = $match[1] ?? null;
                    @endphp

                    @if($videoId)
                    <div>
                        <h4 class="font-semibold text-lg text-gray-800">{{ $recording['title'] ?: 'Watch Recording' }}</h4>
                        <div class="mt-2 relative w-full" style="padding-bottom: 56.25%;"> {{-- Aspect Ratio 16:9 --}}
                            <iframe class="absolute top-0 left-0 w-full h-full rounded-lg shadow"
                                src="https://www.youtube.com/embed/{{ $videoId }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                    @endif
                    @endif
                    @endforeach
                </div>
                @else
                {{-- JIKA TIDAK ADA REKAMAN --}}
                <div class="text-center py-10">
                    <svg class="mx-auto h-12 w-12 text-gray-400" enable-background="new 0 0 512 512" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" id="fi_19024005">
                        <g id="Layer_31" display="none">
                            <g display="inline">
                                <g>
                                    <g fill="none" stroke-miterlimit="10">
                                        <g stroke="#b3b3b3">
                                            <ellipse cx="256" cy="256" rx="248.1" ry="248.3"></ellipse>
                                            <path d="m398.8 504.5h-285.6c-18.8 0-34.1-15.3-34.1-34.1v-428.8c0-18.8 15.3-34.1 34.1-34.1h285.5c18.8 0 34.1 15.3 34.1 34.1v428.9c.1 18.8-15.2 34-34 34z"></path>
                                            <path d="m7.9 399.8v-287.6c0-16.4 13.3-29.8 29.8-29.8h436.7c16.4 0 29.8 13.3 29.8 29.8v287.6c0 16.4-13.3 29.8-29.8 29.8h-436.8c-16.4 0-29.7-13.4-29.7-29.8z"></path>
                                            <path d="m440.4 469.9h-368.8c-16.4 0-29.8-13.3-29.8-29.8v-368.2c0-16.4 13.3-29.8 29.8-29.8h368.8c16.4 0 29.8 13.3 29.8 29.8v368.2c0 16.4-13.4 29.8-29.8 29.8z"></path>
                                        </g>
                                        <path d="m7.5 7.5h497v497h-497z" stroke="#ed1c24" transform="matrix(0 1 -1 0 512 0)"></path>
                                    </g>
                                </g>
                            </g>
                        </g>
                        <g id="Layer_32">
                            <g>
                                <g>
                                    <g>
                                        <g>
                                            <path d="m467.4 498.2h-422.8c-22.1 0-40-17.9-40-40v-220.1c0-22.1 17.9-40 40-40h422.9c22.1 0 40 17.9 40 40v220.1c-.1 22.1-18 40-40.1 40z" fill="#ff5751"></path>
                                            <path d="m29.8 458.2v-220.1c0-22.1 17.9-40 40-40h-25.2c-22.1 0-40 17.9-40 40v220.1c0 22.1 17.9 40 40 40h25.3c-22.2 0-40.1-17.9-40.1-40z" fill="#ff908a"></path>
                                            <path d="m45.1 440.2v-184.1c0-9.7 7.8-17.5 17.5-17.5h386.9c9.7 0 17.5 7.8 17.5 17.5v184.1c0 9.7-7.8 17.5-17.5 17.5h-386.9c-9.7 0-17.5-7.9-17.5-17.5z" fill="#def6fc"></path>
                                            <path d="m70.3 440.2v-184.1c0-9.7 7.8-17.5 17.5-17.5h-25.2c-9.7 0-17.5 7.8-17.5 17.5v184.1c0 9.7 7.8 17.5 17.5 17.5h25.2c-9.7 0-17.5-7.9-17.5-17.5z" fill="#fff"></path>
                                        </g>
                                        <circle cx="256" cy="44.5" fill="#ff5751" r="30.7"></circle>
                                    </g>
                                    <g fill="#133260">
                                        <path d="m115.6 305.7c-12.3 0-19 6.4-19 20.3v43.1c0 13.9 6.7 20.3 19.5 20.3 15.9 0 17.3-10.3 18.2-17 .3-2.9 2.4-4.2 6-4.2 4 0 6 1.3 6 6.7 0 14.2-11.9 25.3-31.1 25.3-16.7 0-30.6-8.4-30.6-31.2v-43c0-22.8 14-31.2 30.8-31.2 19.2 0 30.9 10.9 30.9 24.9 0 5.4-2 6.6-6 6.6-3.9 0-5.9-1.6-6-4.2-.3-5.7-2.6-16.4-18.7-16.4z"></path>
                                        <path d="m167 399.4c-2.7 0-5.4-1.4-5.4-4.3v-96.2c0-2.9 3-4.2 6-4.2s6 1.3 6 4.2v89.6h39.1c2.6 0 3.9 2.7 3.9 5.4s-1.3 5.4-3.9 5.4h-45.7z"></path>
                                        <path d="m225.6 369.1v-43.1c0-22.8 13.5-31.2 30.8-31.2s30.9 8.4 30.9 31.2v43.1c0 22.8-13.6 31.2-30.9 31.2s-30.8-8.5-30.8-31.2zm49.6-43.1c0-13.9-7.2-20.3-18.9-20.3s-18.7 6.4-18.7 20.3v43.1c0 13.9 7 20.3 18.7 20.3s18.9-6.4 18.9-20.3z"></path>
                                        <path d="m350.8 371.6c0-28.8-47.1-15.7-47.1-50.5 0-21 17.3-26.6 31.5-26.6 10.2 0 25 3 25 9.4 0 2.3-1.9 6.6-5.2 6.6s-7.4-5.4-19.9-5.4c-10.6 0-19.5 3.9-19.5 14.9 0 24.2 47.1 11.6 47.1 50.5 0 20.5-13.2 30.1-31.3 30.1-18.5 0-30.3-10-30.3-16.2 0-2.9 2.6-6.6 5.3-6.6 4.3 0 7.6 11.6 24.5 11.6 12 0 19.9-5.9 19.9-17.8z"></path>
                                        <path d="m390.4 342h22.3c2.6 0 4.2 2.4 4.2 5.2 0 2.3-1.3 4.9-4.2 4.9h-22.3v36.5h44.4c2.6 0 4.2 2.6 4.2 5.6 0 2.6-1.3 5.3-4.2 5.3h-51c-2.7 0-5.4-1.4-5.4-4.3v-96c0-2.9 2.7-4.3 5.4-4.3h51c2.9 0 4.2 2.7 4.2 5.3 0 3-1.6 5.6-4.2 5.6h-44.4z"></path>
                                    </g>
                                </g>
                                <g fill="#133260">
                                    <path d="m467.4 193.5h-24.7l-151.6-151.6c-1.3-18.2-16.5-32.6-35.1-32.6s-33.8 14.4-35.1 32.7l-151.6 151.5h-24.7c-24.6 0-44.6 20-44.6 44.6v220.1c0 24.6 20 44.6 44.6 44.6h422.9c24.6 0 44.6-20 44.6-44.6v-220.1c-.1-24.6-20.1-44.6-44.7-44.6zm-237.5-149.5c.2-14.2 11.9-25.7 26.1-25.7s25.9 11.5 26.1 25.7v.5c0 14.4-11.7 26.1-26.1 26.1s-26.1-11.7-26.1-26.1zm-7.9 9.7c4.1 15 17.8 26 34 26s29.9-11 34-26l139.8 139.8h-347.6zm280.9 404.5c0 19.5-15.9 35.4-35.4 35.4h-422.9c-19.5 0-35.4-15.9-35.4-35.4v-220.1c0-19.5 15.9-35.4 35.4-35.4h422.9c19.5 0 35.4 15.9 35.4 35.4z"></path>
                                    <path d="m449.4 234h-386.8c-12.2 0-22.1 9.9-22.1 22.1v184.1c0 12.2 9.9 22.1 22.1 22.1h386.9c12.2 0 22.1-9.9 22.1-22.1v-184.1c-.1-12.2-10-22.1-22.2-22.1zm13 206.2c0 7.1-5.8 13-13 13h-386.8c-7.1 0-13-5.8-13-13v-184.1c0-7.1 5.8-13 13-13h386.9c7.1 0 13 5.8 13 13v184.1z"></path>
                                </g>
                            </g>
                        </g>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">This Event Has Concluded</h3>
                    <p class="mt-1 text-sm text-gray-500">Thank you for your interest. This event has already taken place.</p>
                </div>
                @endif

                @elseif ($event->quota > 0 && $event->remaining_quota <= 0)
                    {{-- KONDISI 3: KUOTA PENUH (TAMPILAN SVG PENUH) --}}
                    <div class="text-center py-10">
                    <svg class="mx-auto h-16 w-16 text-gray-400" id="fi_9743127" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                        <path d="m501.657 150.95-140.593-140.593a8 8 0 0 0 -5.657-2.343h-196.49a8 8 0 0 0 -5.61 2.3l-142.917 140.586a8 8 0 0 0 -2.39 5.7v198.821a8 8 0 0 0 2.39 5.7l142.918 140.568a8 8 0 0 0 5.609 2.3h196.49a8 8 0 0 0 5.656-2.343l140.593-140.567a8 8 0 0 0 2.344-5.658v-198.814a8 8 0 0 0 -2.343-5.657zm-13.657 201.157-135.906 135.879h-189.9l-138.194-135.917v-192.11l138.193-135.945h189.9l135.907 135.906zm-149.062-304.324h-163.92a8 8 0 0 0 -5.61 2.3l-119.249 117.289a8 8 0 0 0 -2.39 5.7v165.881a8 8 0 0 0 2.39 5.7l44.955 44.219a8 8 0 0 0 11.268-.048l58.546-58.57c1.241 4.135 2.514 8.2 3.83 12.156a104.047 104.047 0 0 0 5.112 12.536l-50.457 50.457a8 8 0 0 0 .047 11.361l45.948 45.183a8 8 0 0 0 5.61 2.3h163.92a8 8 0 0 0 5.662-2.347l117.288-117.291a8 8 0 0 0 2.343-5.656v-165.877a8 8 0 0 0 -2.343-5.657l-44.588-44.587a8 8 0 0 0 -11.313 0l-51.587 51.592a25.547 25.547 0 0 0 -19.418-.066v-14.126l54.206-54.206a8 8 0 0 0 0-11.313l-44.588-44.587a8 8 0 0 0 -5.662-2.343zm72.707 92.017 36.586 36.587v159.25l-112.606 112.609h-157.333l-37.861-37.231 41.815-41.815a103.368 103.368 0 0 0 187.964-59.387v-111.713a25.491 25.491 0 0 0 -3.667-13.2zm-67.038 48.672a9.627 9.627 0 0 1 9.6 9.63v111.711a87.365 87.365 0 0 1 -170.269 27.551c-6.532-19.651-12.113-42.225-17.51-64.055-2.676-10.823-5.443-22.015-8.233-32.43a12.816 12.816 0 0 1 8.947-15.49 12.685 12.685 0 0 1 15.48 8.942l12.727 47.507a8 8 0 0 0 7.833 5.929c22.833-.341 49.287 16.358 53.357 47.818a8 8 0 0 0 15.868-2.052c-4.786-36.99-33.745-58.123-61.33-61.33v-119.876a12.649 12.649 0 0 1 25.3 0v101.618a8 8 0 0 0 16 0v-126.445a12.65 12.65 0 0 1 25.3 0v126.445a8 8 0 1 0 16 0v-100.6a12.65 12.65 0 0 1 25.3 0v100.6a8 8 0 1 0 16 0v-55.845a9.641 9.641 0 0 1 9.63-9.626zm-12.631-47.868a28.617 28.617 0 0 0 -38.3-12.939v-.17a28.645 28.645 0 0 0 -57.278-.848 28.643 28.643 0 0 0 -41.32 25.678v70.709a28.647 28.647 0 0 0 -52.337 21.981c2.75 10.267 5.376 20.888 8.155 32.132 2.914 11.782 5.881 23.775 9.034 35.474l-59.254 59.279-36.907-36.3v-159.172l114.523-112.645h157.333l36.587 36.587z"></path>
                        <path d="m162.193 24.014-138.193 135.945v192.11l138.192 135.917h189.9l135.908-135.879v-192.187l-135.907-135.906zm229.333 76.356a8 8 0 0 1 -2.343 5.656l-54.206 54.206v14.126a25.547 25.547 0 0 1 19.418.066l51.593-51.592a8 8 0 0 1 11.313 0l44.587 44.587a8 8 0 0 1 2.343 5.657v165.877a8 8 0 0 1 -2.343 5.656l-117.288 117.291a8 8 0 0 1 -5.657 2.344h-163.925a8 8 0 0 1 -5.61-2.3l-45.948-45.177a8 8 0 0 1 -.047-11.361l50.457-50.457a104.047 104.047 0 0 1 -5.112-12.536c-1.316-3.959-2.589-8.021-3.83-12.156l-58.546 58.57a8 8 0 0 1 -11.268.048l-44.955-44.219a8 8 0 0 1 -2.39-5.7v-165.88a8 8 0 0 1 2.39-5.7l119.249-117.297a8 8 0 0 1 5.61-2.3h163.92a8 8 0 0 1 5.657 2.343l44.588 44.587a8 8 0 0 1 2.343 5.661z" fill="#ff6969"></path>
                        <path d="m334.977 253.945a8 8 0 1 1 -16 0v-100.6a12.65 12.65 0 0 0 -25.3 0v100.6a8 8 0 1 1 -16 0v-126.445a12.65 12.65 0 0 0 -25.3 0v126.445a8 8 0 0 1 -16 0v-101.618a12.649 12.649 0 0 0 -25.3 0v119.873c27.585 3.207 56.544 24.34 61.33 61.33a8 8 0 0 1 -15.868 2.052c-4.07-31.46-30.524-48.159-53.357-47.818a8 8 0 0 1 -7.833-5.929l-12.727-47.507a12.685 12.685 0 0 0 -15.48-8.942 12.816 12.816 0 0 0 -8.947 15.49c2.79 10.415 5.557 21.607 8.233 32.43 5.4 21.83 10.978 44.4 17.51 64.055a87.365 87.365 0 0 0 170.272-27.548v-111.713a9.617 9.617 0 1 0 -19.233 0z" fill="#ffd886"></path>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-red-900">Pendaftaran Penuh</h3>
                    <p class="mt-1 text-sm text-gray-500">Mohon maaf, kuota untuk event ini sudah terpenuhi.</p>
        </div>

        @else
        {{-- JIKA SEMUA AMAN, TAMPILKAN FORM --}}
        <form method="POST" action="{{ route('invitation.submit', $invitation->uuid) }}" x-data="{ 
                    status: 'confirmed', 
                    eventType: '{{ $event->type }}'
                }">
            @csrf

            {{-- Sapaan --}}
            <div class="mb-6">
                <p class="text-gray-700">Halo, <strong>{{ $invitation->name }}</strong> ({{ $invitation->company ?? 'Tamu Undangan' }}).</p>
                <p class="text-sm text-gray-500">Mohon berikan konfirmasi kehadiran Anda di bawah ini:</p>
            </div>

            {{-- Pilihan Status (Radio Cards) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                {{-- 1. Hadir --}}
                <label class="cursor-pointer">
                    <input type="radio" name="response_status" value="confirmed" x-model="status" class="peer sr-only">
                    <div class="rounded-lg border-2 border-gray-200 p-4 hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all text-center h-full">
                        <div class="text-2xl mb-1">✅</div>
                        <div class="font-semibold text-gray-700">Bersedia Hadir</div>
                    </div>
                </label>

                {{-- 2. Diwakilkan --}}
                <label class="cursor-pointer">
                    <input type="radio" name="response_status" value="represented" x-model="status" class="peer sr-only">
                    <div class="rounded-lg border-2 border-gray-200 p-4 hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all text-center h-full">
                        <div class="text-2xl mb-1">🔄</div>
                        <div class="font-semibold text-gray-700">Diwakilkan</div>
                    </div>
                </label>

                {{-- 3. Tidak Hadir --}}
                <label class="cursor-pointer">
                    <input type="radio" name="response_status" value="declined" x-model="status" class="peer sr-only">
                    <div class="rounded-lg border-2 border-gray-200 p-4 hover:bg-gray-50 peer-checked:border-red-500 peer-checked:bg-red-50 transition-all text-center h-full">
                        <div class="text-2xl mb-1">❌</div>
                        <div class="font-semibold text-gray-700">Tidak Bisa Hadir</div>
                    </div>
                </label>
            </div>

            {{-- FORM: BERSEDIA HADIR --}}
            <div x-show="status === 'confirmed'" x-transition>
                <h3 class="font-semibold text-gray-800 border-b pb-2 mb-4">Konfirmasi Data Diri</h3>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <x-input-label for="name" value="Nama Lengkap" />
                        <x-text-input id="name" class="block mt-1 w-full bg-gray-50" type="text" name="name" value="{{ $invitation->name }}" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="email" value="Email (Untuk pengiriman tiket)" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ $invitation->email }}" required />
                        </div>
                        <div>
                            <x-input-label for="phone" value="No. WhatsApp" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" value="{{ $invitation->phone_number }}" required />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="jabatan" value="Jabatan / Posisi" />
                        <x-text-input id="jabatan" class="block mt-1 w-full" type="text" name="jabatan" value="{{ $invitation->category }}" />
                    </div>
                </div>
            </div>

            {{-- FORM: DIWAKILKAN --}}
            <div x-show="status === 'represented'" x-transition style="display: none;">
                <h3 class="font-semibold text-gray-800 border-b pb-2 mb-4">Data Perwakilan</h3>
                <p class="text-sm text-gray-600 mb-4">Mohon isi data orang yang akan mewakili Anda:</p>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <x-input-label for="rep_name" value="Nama Lengkap Wakil" />
                        <x-text-input id="rep_name" class="block mt-1 w-full" type="text" name="rep_name" placeholder="Nama perwakilan..." />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="rep_email" value="Email Wakil" />
                            <x-text-input id="rep_email" class="block mt-1 w-full" type="email" name="rep_email" placeholder="email@contoh.com" />
                        </div>
                        <div>
                            <x-input-label for="rep_phone" value="No. WhatsApp Wakil" />
                            <x-text-input id="rep_phone" class="block mt-1 w-full" type="text" name="rep_phone" placeholder="08..." />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="rep_jabatan" value="Jabatan Wakil" />
                        <x-text-input id="rep_jabatan" class="block mt-1 w-full" type="text" name="rep_jabatan" placeholder="Staf / Asisten / dll" />
                    </div>
                </div>
            </div>

            {{-- FORM: TIDAK HADIR --}}
            <div x-show="status === 'declined'" x-transition style="display: none;">
                <h3 class="font-semibold text-red-600 border-b pb-2 mb-4">Konfirmasi Ketidakhadiran</h3>
                <div>
                    <x-input-label for="rejection_reason" value="Alasan (Opsional)" />
                    <textarea id="rejection_reason" name="rejection_reason" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Mohon maaf saya tidak bisa hadir karena..."></textarea>
                </div>
            </div>

            {{-- OPSI TAMBAHAN: HYBRID ATTENDANCE --}}
            <div x-show="status !== 'declined' && eventType === 'hybrid'" class="mt-6 pt-4 border-t" style="display: none;">
                <h3 class="font-semibold text-gray-800 mb-2">Metode Kehadiran</h3>
                <div class="flex gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="attendance_type" value="offline" class="text-indigo-600 border-gray-300 focus:ring-indigo-500" checked>
                        <span class="ml-2">Hadir Langsung (Offline)</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="attendance_type" value="online" class="text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <span class="ml-2">
                            Hadir Online
                            ({{ $event->platform === 'Lainnya...' && isset($event->meeting_info['platform_name']) 
                                        ? $event->meeting_info['platform_name'] 
                                        : ($event->platform ?? 'Online') }})
                        </span>
                    </label>
                </div>
            </div>

            {{-- TOMBOL SUBMIT --}}
            <div class="mt-8">
                <x-primary-button class="w-full justify-center py-3 text-lg">
                    Kirim Konfirmasi
                </x-primary-button>
            </div>

        </form>
        @endif
        {{-- ▲▲▲ SELESAI LOGIKA PENGECEKAN ▲▲▲ --}}

    </div>
    </div>
</x-guest-layout>