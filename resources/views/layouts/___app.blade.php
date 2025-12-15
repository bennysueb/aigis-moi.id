<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Pengaturan Meta Tag Dinamis --}}
    <title>{{ config('settings.meta_title', config('app.name')) }}</title>
    <meta name="description" content="{{ config('settings.meta_description') }}">
    <meta name="keywords" content="{{ config('settings.meta_keywords') }}">

    <meta property="og:title" content="Virtual EHEF Indonesia - Study in Europe">
    <meta property="og:description" content="Discover European universities, study programs, and scholarship opportunities through Virtual EHEF Indonesia — the online education fair for Indonesian students.">
    <meta property="og:image" content="{{ asset('storage/' . config('settings.app_favicon')) }}">
    <meta property="og:url" content="https://virtual.ehef.id">
    <meta property="og:type" content="website">


    {{-- Favicon Dinamis --}}
    @if(config('settings.app_favicon'))
    <link rel="icon" href="{{ asset('storage/' . config('settings.app_favicon')) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Scripts -->

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
    <style>
        /*
            * Perbaikan untuk Alignment Checkbox Tom Select
            * Membuat checkbox selalu rata atas dengan baris pertama teks.
            */
        .ts-dropdown .option {
            display: flex;
            align-items: flex-start;
            /* Mengubah alignment vertikal ke atas */
        }

        .ts-dropdown .option input[type="checkbox"] {
            margin-top: 0.2rem;
            /* Menyesuaikan posisi checkbox sedikit ke bawah agar pas */
            flex-shrink: 0;
            /* Mencegah checkbox mengecil jika ruang sempit */
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}">
    </script>
</head>

<body class="font-sans antialiased" x-on:livewire:navigate.window="document.querySelectorAll('.livewire-persistent-modal').forEach(el => el.remove())">

    <livewire:layout.navigation />
    <div class="min-h-screen bg-gray-100">
        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main>

            {{ $slot }}

        </main>
    </div>

    <footer class="bg-green-light text-white">
        <div class="max-w-screen-2xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <a href="{{ route('home') }}" wire:navigate>
                            {{-- Cek apakah ada logo yang diunggah --}}
                            @if(config('settings.footer_logo'))
                            {{-- Jika ada, tampilkan gambar logo --}}
                            <img class="h-14 w-auto mr-3" src="{{ asset('storage/' . config('settings.footer_logo')) }}" alt="{{ config('settings.app_name', 'Logo') }}">
                            @else
                            {{-- Jika tidak ada, tampilkan teks nama aplikasi --}}
                            <span class="text-white font-bold text-xl">{{ config('settings.app_name', 'Registrasi.Events') }}</span>
                            @endif
                        </a>
                    </div>
                    <p class="max-w-xs">
                        {{-- Anda bisa membuat ini dinamis juga melalui Application Settings jika perlu --}}
                        {{ __('welcome.aigis_description_footer') }}
                    </p>
                </div>
                {{-- Kolom Navigasi Dinamis --}}
                @if($footerNavigation->isNotEmpty())
                <div>
                    <h3 class="text-white font-semibold tracking-wider uppercase mb-4">{{ __('welcome.navigation') }}</h3>
                    <nav class="space-y-2">
                        @foreach($footerNavigation as $item)
                        <a href="{{ url($item->link) }}" target="{{ $item->target }}" class="block hover:text-green-dark transition-colors">{{ $item->label }}</a>
                        @endforeach
                    </nav>
                </div>
                @endif

                {{-- Kolom Legal Dinamis --}}
                @if($footerLegal->isNotEmpty())
                <div>
                    <h3 class="text-white font-semibold tracking-wider uppercase mb-4">{{ __('welcome.legal') }}</h3>
                    <nav class="space-y-2">
                        @foreach($footerLegal as $item)
                        {{-- Gunakan route() jika link-nya adalah nama rute, atau url() untuk link biasa --}}
                        <a href="{{ $item->link === '#' ? '#' : (Route::has($item->link) ? route($item->link) : url($item->link)) }}" target="{{ $item->target }}" class="block hover:text-white transition-colors">{{ $item->label }}</a>
                        @endforeach
                    </nav>
                </div>
                @endif

                {{-- Kolom Kontak & Sosial Media Dinamis --}}
                <div>
                    <h3 class="text-white font-semibold tracking-wider uppercase mb-4">{{ __('welcome.contact_us') }}</h3>

                    {{-- Ikon Media Sosial --}}
                    <div class="flex space-x-4 mb-4">

                        @if(config('settings.footer_facebook_url'))
                        <a href="{{ config('settings.footer_facebook_url') }}" target="_blank" class="hover:text-primary transition-colors text-xl">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        @endif

                        @if(config('settings.footer_instagram_url'))
                        <a href="{{ config('settings.footer_instagram_url') }}" target="_blank" class="hover:text-primary transition-colors text-xl">
                            <i class="fab fa-instagram"></i>
                        </a>
                        @endif

                        {{-- DIGANTI: Dari Twitter menjadi Wikipedia --}}
                        @if(config('settings.footer_wikipedia_url'))
                        <a href="{{ config('settings.footer_wikipedia_url') }}" target="_blank" class="hover:text-primary transition-colors text-xl">
                            <i class="fab fa-wikipedia-w"></i>
                        </a>
                        @endif

                        @if(config('settings.footer_youtube_url'))
                        <a href="{{ config('settings.footer_youtube_url') }}" target="_blank" class="hover:text-primary transition-colors text-xl">
                            <i class="fab fa-youtube"></i>
                        </a>
                        @endif

                        {{-- DITAMBAHKAN: Link ikon WhatsApp --}}
                        @if(config('settings.footer_whatsapp'))
                        {{-- Ini mengasumsikan nomor disimpan dalam format internasional, cth: 628123456789 --}}
                        <a href="https://wa.me/{{ config('settings.footer_whatsapp') }}" target="_blank" class="hover:text-primary transition-colors text-xl">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        @endif

                        {{-- DITAMBAHKAN: Link ikon Email --}}
                        @if(config('settings.footer_email'))
                        {{-- Ini mengasumsikan nomor disimpan dalam format internasional, cth: 628123456789 --}}
                        <a href="mailto:{{ config('settings.footer_email') }}" target="_blank" class="hover:text-primary transition-colors text-xl">
                            <i class="fas fa-envelope"></i>
                        </a>
                        @endif

                    </div>

                    {{-- Info Kontak Teks --}}
                    <div class="space-y-2">

                        @if(config('settings.footer_email'))
                        <div class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>{{ config('settings.footer_email') }}</span>
                        </div>
                        @endif

                        @if(config('settings.footer_phone'))
                        <div class="flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            <span>{{ config('settings.footer_phone') }}</span>
                        </div>
                        @endif

                        {{-- DITAMBAHKAN: Teks nomor WhatsApp --}}
                        @if(config('settings.footer_whatsapp'))
                        <div class="flex items-center">
                            <i class="fab fa-whatsapp mr-2"></i> {{-- Menggunakan ikon 'fab' agar konsisten --}}
                            <span>{{ config('settings.footer_whatsapp') }}</span>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-700 text-center text-green-light text-sm bg-green-dark p-6 text-footer">
            <p>&copy; {{ date('Y') }} {{ config('settings.app_name', 'AIGIS Platform') }}. {{ __('welcome.all_rights_reserved') }}</p>
        </div>
    </footer>

    @livewireScripts

    @stack('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        // Inisialisasi Fancybox
        Fancybox.bind("[data-fancybox]", {
            // Opsi kustom jika perlu
        });
    </script>


    <script data-host="https://analytics.gmsconsolidate.id" data-dnt="false" src="https://analytics.gmsconsolidate.id/js/script.js" id="ZwSg9rf6GA" async defer></script>


    <div
        x-data="{ show: false, message: '' }"
        x-on:notify.window="message = $event.detail; show = true; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed top-5 right-5 z-50 bg-green-500 text-white px-4 py-2 rounded-lg shadow-md"
        style="display: none;">
        <span x-text="message"></span>
    </div>

</body>

</html>