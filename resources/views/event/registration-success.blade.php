<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 md:p-12">
                    {{-- Animasi Ikon Sukses --}}
                    <div class="svg-container mx-auto mb-6 h-24 w-24">
                        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                            <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                            <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                        </svg>
                    </div>

                    <div class="space-y-4">
                        <h2 class="text-3xl font-extrabold font-serif text-gray-900">{{ __('messages.registration_successful') }}</h2>
                        <p class="text-lg text-gray-600">
                            {{ __('messages.thank_you_for_registering', ['eventName' => $event->name]) }}
                        </p>

                        @if($registration->attendance_type == 'offline')
                            <p class="text-gray-500 max-w-md mx-auto">
                                An e-ticket has been sent to your email. Please present the ticket (QR Code) when re-registering at the event location.
                            </p>
                        @elseif($registration->attendance_type == 'online')
                            <p class="text-gray-500 max-w-md mx-auto">
                                Information for joining online has been sent to your email. Please use the link below to enter the virtual event space.
                            </p>

                            @if($event->meeting_link)
                                <div class="mt-4 bg-gray-50 border rounded-lg p-4 text-left">
                                    <h4 class="font-semibold text-gray-800">Online Event Details:</h4>
                                    <ul class="text-sm text-gray-600 list-disc list-inside mt-2">
                                        <li><strong>Join Link:</strong> <a href="{{ $event->meeting_link }}" target="_blank" class="text-blue-600 hover:underline">Click here</a></li>
                                        @if($event->meeting_info && isset($event->meeting_info['meeting_id']))
                                            <li><strong>Meeting ID:</strong> {{ $event->meeting_info['meeting_id'] }}</li>
                                        @endif
                                        @if($event->meeting_info && isset($event->meeting_info['passcode']))
                                            <li><strong>Passcode:</strong> {{ $event->meeting_info['passcode'] }}</li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        @else
                            <p class="text-gray-500 max-w-md mx-auto">
                                {{ __('messages.confirmation_notice') }}
                            </p>
                        @endif
                    </div>

                    {{-- ▼▼▼ AREA TOMBOL (YANG DIMODIFIKASI) ▼▼▼ --}}
                    <div class="mt-12 flex flex-col sm:flex-row justify-center gap-4">
                        
                        {{-- 1. Tombol Tiket / Join (Sesuai Tipe Kehadiran) --}}
                        @if($registration->attendance_type == 'offline')
                            <a href="{{ route('tickets.qrcode', $registration->uuid) }}" target="_blank"
                               class="inline-block bg-primary text-white font-bold py-3 px-6 rounded-lg text-base hover:bg-opacity-90 transition-colors">
                                View E-Ticket (QR)
                            </a>
                        @elseif($registration->attendance_type == 'online' && $event->meeting_link)
                            <a href="{{ $event->meeting_link }}" target="_blank"
                               class="inline-block bg-green-600 text-white font-bold py-3 px-6 rounded-lg text-base hover:bg-green-700 transition-colors">
                                Join Now
                            </a>
                        @endif

                        {{-- 2. Tombol Invoice (HANYA MUNCUL JIKA EVENT BERBAYAR) --}}
                        @if($event->is_paid_event)
                            {{-- Pastikan route 'invoice.show' atau 'public.invoice' sudah ada di web.php --}}
                            {{-- Jika belum ada route name, bisa pakai url('/invoice/' . $registration->uuid) --}}
                            <a href="{{ url('/invoice/' . $registration->uuid) }}" target="_blank"
                               class="inline-block bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg text-base hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-file-invoice mr-2"></i> View Invoice
                            </a>
                        @endif

                        {{-- 3. Tombol Home --}}
                        <a href="{{ route('events.index') }}"
                           class="inline-block bg-gray-200 text-gray-800 font-bold py-3 px-6 rounded-lg text-base hover:bg-gray-300 transition-colors">
                            See Other Events
                        </a>
                    </div>
                    {{-- ▲▲▲ SELESAI AREA TOMBOL ▲▲▲ --}}


                    <div class="mt-12 pt-8 border-t">
                        <p class="text-base text-gray-600">
                            {{ __('messages.follow_us_notice') }}
                        </p>
                        <div class="mt-4 flex justify-center items-center space-x-4 sm:space-x-8">
                            {{-- Instagram Links (Sama seperti sebelumnya) --}}
                            <a href="http://instagram.com/ehef.id" target="_blank" class="flex items-center space-x-2 text-gray-500 hover:opacity-80 transition-opacity">
                                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><defs><linearGradient id="insta-gradient" x1="0%" y1="100%" x2="100%" y2="0%"><stop offset="0%" style="stop-color:#feda75;stop-opacity:1" /><stop offset="25%" style="stop-color:#fa7e1e;stop-opacity:1" /><stop offset="50%" style="stop-color:#d62976;stop-opacity:1" /><stop offset="75%" style="stop-color:#962fbf;stop-opacity:1" /><stop offset="100%" style="stop-color:#4f5bd5;stop-opacity:1" /></linearGradient></defs><path fill="url(#insta-gradient)" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.85s-.011 3.584-.069 4.85c-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07s-3.584-.012-4.85-.07c-3.252-.148-4.771-1.691-4.919-4.919-.058-1.265-.069-1.645-.069-4.85s.011-3.584.069-4.85c.149-3.225 1.664-4.771 4.919-4.919C8.416 2.175 8.796 2.163 12 2.163zm0 1.442c-3.115 0-3.479.012-4.694.068-2.61.12-3.834 1.34-3.954 3.954-.056 1.216-.067 1.576-.067 4.694s.011 3.479.067 4.694c.12 2.61 1.344 3.834 3.954 3.954 1.216.056 1.579.068 4.694.068s3.479-.012 4.694-.068c2.61-.12 3.834-1.34 3.954-3.954.056-1.216.067-1.576.067-4.694s-.011-3.479-.067-4.694c-.12-2.61-1.344-3.834-3.954-3.954-1.216-.056-1.579-.068-4.694-.068zm0 3.199a5.196 5.196 0 100 10.392 5.196 5.196 0 000-10.392zm0 1.442a3.754 3.754 0 110 7.508 3.754 3.754 0 010-7.508zm5.245-3.332a1.2 1.2 0 100 2.4 1.2 1.2 0 000-2.4z" /></svg>
                                <span class="font-semibold text-sm">@ehef.id</span>
                            </a>
                            <a href="http://instagram.com/uni_eropa" target="_blank" class="flex items-center space-x-2 text-gray-500 hover:opacity-80 transition-opacity">
                                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><defs><linearGradient id="insta-gradient" x1="0%" y1="100%" x2="100%" y2="0%"><stop offset="0%" style="stop-color:#feda75;stop-opacity:1" /><stop offset="25%" style="stop-color:#fa7e1e;stop-opacity:1" /><stop offset="50%" style="stop-color:#d62976;stop-opacity:1" /><stop offset="75%" style="stop-color:#962fbf;stop-opacity:1" /><stop offset="100%" style="stop-color:#4f5bd5;stop-opacity:1" /></linearGradient></defs><path fill="url(#insta-gradient)" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.85s-.011 3.584-.069 4.85c-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07s-3.584-.012-4.85-.07c-3.252-.148-4.771-1.691-4.919-4.919-.058-1.265-.069-1.645-.069-4.85s.011-3.584.069-4.85c.149-3.225 1.664-4.771 4.919-4.919C8.416 2.175 8.796 2.163 12 2.163zm0 1.442c-3.115 0-3.479.012-4.694.068-2.61.12-3.834 1.34-3.954 3.954-.056 1.216-.067 1.576-.067 4.694s.011 3.479.067 4.694c.12 2.61 1.344 3.834 3.954 3.954 1.216.056 1.579.068 4.694.068s3.479-.012 4.694-.068c2.61-.12 3.834-1.34 3.954-3.954.056-1.216.067-1.576.067-4.694s-.011-3.479-.067-4.694c-.12-2.61-1.344-3.834-3.954-3.954-1.216-.056-1.579-.068-4.694-.068zm0 3.199a5.196 5.196 0 100 10.392 5.196 5.196 0 000-10.392zm0 1.442a3.754 3.754 0 110 7.508 3.754 3.754 0 010-7.508zm5.245-3.332a1.2 1.2 0 100 2.4 1.2 1.2 0 000-2.4z" /></svg>
                                <span class="font-semibold text-sm">@uni_eropa</span>
                            </a>
                            <a href="http://instagram.com/erasmusplus.id" target="_blank" class="flex items-center space-x-2 text-gray-500 hover:opacity-80 transition-opacity">
                                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><defs><linearGradient id="insta-gradient" x1="0%" y1="100%" x2="100%" y2="0%"><stop offset="0%" style="stop-color:#feda75;stop-opacity:1" /><stop offset="25%" style="stop-color:#fa7e1e;stop-opacity:1" /><stop offset="50%" style="stop-color:#d62976;stop-opacity:1" /><stop offset="75%" style="stop-color:#962fbf;stop-opacity:1" /><stop offset="100%" style="stop-color:#4f5bd5;stop-opacity:1" /></linearGradient></defs><path fill="url(#insta-gradient)" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.85s-.011 3.584-.069 4.85c-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07s-3.584-.012-4.85-.07c-3.252-.148-4.771-1.691-4.919-4.919-.058-1.265-.069-1.645-.069-4.85s.011-3.584.069-4.85c.149-3.225 1.664-4.771 4.919-4.919C8.416 2.175 8.796 2.163 12 2.163zm0 1.442c-3.115 0-3.479.012-4.694.068-2.61.12-3.834 1.34-3.954 3.954-.056 1.216-.067 1.576-.067 4.694s.011 3.479.067 4.694c.12 2.61 1.344 3.834 3.954 3.954 1.216.056 1.579.068 4.694.068s3.479-.012 4.694-.068c2.61-.12 3.834-1.34 3.954-3.954.056-1.216.067-1.576.067-4.694s-.011-3.479-.067-4.694c-.12-2.61-1.344-3.834-3.954-3.954-1.216-.056-1.579-.068-4.694-.068zm0 3.199a5.196 5.196 0 100 10.392 5.196 5.196 0 000-10.392zm0 1.442a3.754 3.754 0 110 7.508 3.754 3.754 0 010-7.508zm5.245-3.332a1.2 1.2 0 100 2.4 1.2 1.2 0 000-2.4z" /></svg>
                                <span class="font-semibold text-sm">@erasmusplus.id</span>
                            </a>
                        </div>
                        <div class="mt-4 flex justify-center items-center space-x-4 sm:space-x-8">
                            <a href="https://virtual.ehef.id/" target="_blank" class="flex items-center space-x-2 text-gray-500 hover:opacity-80 transition-opacity">
                                <svg width="30px" height="30px" viewBox="0 0 32 32" data-name="Layer 1" id="Layer_1" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.7434,22.505A12.9769,12.9769,0,0,0,14.88,28.949l5.8848-10.1927L16,16.0058,11.2385,18.755l-1.5875-2.75L8.4885,13.9919,5.3553,8.5649A12.9894,12.9894,0,0,0,4.7434,22.505Z" fill="#00ac47" />
                                    <path d="M16,3.0072A12.9769,12.9769,0,0,0,5.3507,8.5636l5.8848,10.1927L16,16.0057V10.5072H27.766A12.99,12.99,0,0,0,16,3.0072Z" fill="#ea4435" />
                                    <path d="M27.2557,22.505a12.9772,12.9772,0,0,0,.5124-12H15.9986v5.5011l4.7619,2.7492-1.5875,2.75-1.1625,2.0135-3.1333,5.4269A12.99,12.99,0,0,0,27.2557,22.505Z" fill="#ffba00" />
                                    <circle cx="15.9995" cy="16.0072" fill="#ffffff" r="5.5" />
                                    <circle cx="15.9995" cy="16.0072" fill="#4285f4" r="4.25" />
                                </svg>
                                <span class="font-semibold text-sm">www.virtual.ehef.id</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kode CSS Animasi --}}
    <style>
        .checkmark { width: 100px; height: 100px; border-radius: 50%; display: block; stroke-width: 3; stroke: #fff; stroke-miterlimit: 10; margin: auto; box-shadow: inset 0px 0px 0px #4baf47; animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both; }
        .checkmark__circle { stroke-dasharray: 166; stroke-dashoffset: 166; stroke-width: 3; stroke-miterlimit: 10; stroke: #4baf47; fill: none; animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards; }
        .checkmark__check { transform-origin: 50% 50%; stroke-dasharray: 48; stroke-dashoffset: 48; animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards; }
        @keyframes stroke { 100% { stroke-dashoffset: 0; } }
        @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
        @keyframes fill { 100% { box-shadow: inset 0px 0px 0px 50px #4baf47; } }
    </style>
</x-app-layout>