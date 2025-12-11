<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use App\Models\MenuItem;

new class extends Component
{
    // public Collection $publicMenuItems;
    public Collection $headerMenuItems;

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    /**
     * Mount the component and fetch the menu items.
     */
    public function mount(): void
    {
        $this->headerMenuItems = MenuItem::where('location', 'header') // Filter berdasarkan lokasi
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('order')
            ->get();
    }
}; ?>




<div x-data="{ scrolled: window.scrollY > 50 }" @scroll.window="scrolled = (window.scrollY > 50)" x-init="scrolled = (window.scrollY > 50)" class="w-full shadow-md sticky top-0 z-40 transition-all duration-300" :class="scrolled ? 'bg-green-dark' : 'bg-green-light'">
    <div class="text-green-800 transition-colors duration-300" :class="scrolled ? 'bg-green-default' : 'bg-green-light'">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" wire:navigate>
                        {{-- Cek apakah ada logo yang diunggah --}}
                        @if(config('settings.app_logo'))
                        {{-- Jika ada, tampilkan gambar logo --}}
                        <img class="block h-16 w-auto" src="{{ asset('storage/' . config('settings.app_logo')) }}" alt="{{ config('settings.app_name', 'Logo') }}">
                        @else
                        {{-- Jika tidak ada, tampilkan teks nama aplikasi --}}
                        <h1 class="font-heading text-xl font-bold">{{ config('settings.app_name', 'Registrasi.Events') }}</h1>
                        @endif
                    </a>
                </div>
                <div>
                    <livewire:language-switcher />
                </div>
            </div>
        </div>
    </div>


    {{-- CATATAN: x-data="{ open: false }" di sini sekarang mengontrol menu mobile --}}
    <nav x-data="{ open: false }" class="border-b border-gray-700 transition-colors duration-300" :class="scrolled ? 'bg-gray-800' : 'bg-gray-800/50'">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex text-white">
                    @guest
                    {{-- MENU PUBLIK BARU --}}
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')" wire:navigate>{{ __('nav.home') }}</x-nav-link>

                        @foreach($headerMenuItems as $item)
                        @if($item->children->isEmpty())
                        <x-nav-link :href="url($item->link)" :active="request()->fullUrlIs(url($item->link))" wire:navigate>
                            {{ $item->label }}
                        </x-nav-link>
                        @else
                        {{-- Dropdown untuk item dengan anak --}}
                        <div class="relative flex" x-data="{ open: false }">
                            <button @click="open = !open" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-gray-300 hover:text-white text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                <span>{{ $item->label }}</span>
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="origin-top-left absolute left-0 mt-12 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50" style="display: none;">
                                <div class="py-1">
                                    @foreach($item->children as $child)
                                    <x-dropdown-link :href="url($child->link)" :active="request()->fullUrlIs(url($child->link))" wire:navigate>{{ $child->label }}</x-dropdown-link>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach

                    </div>
                    @endguest
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    @auth

                    <livewire:layout.notification-bell />

                    {{-- Menu untuk Pengguna Biasa (tidak punya role) --}}
                    @if(Auth::user()->roles->isEmpty())
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>{{ __('nav.my_dashboard') }}</x-nav-link>
                        <x-nav-link :href="route('user.qrcode')" :active="request()->routeIs('user.qrcode')" wire:navigate>
                            {{ __('My Digital Card') }}
                        </x-nav-link>
                        <x-nav-link :href="route('scanner.page')" :active="request()->routeIs('scanner.page')" wire:navigate>{{ __('nav.qr_scanner') }}</x-nav-link>
                        <x-nav-link :href="route('exhibitors.index')" :active="request()->routeIs('exhibitors.index')" wire:navigate>{{ __('nav.exhibitors') }}</x-nav-link>
                    </div>
                    @endif

                    {{-- Menu untuk Admin/Pengguna dengan Peran --}}
                    @if(Auth::user()->roles->isNotEmpty())
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                        @if(Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Admin'))
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" wire:navigate>
                            {{ __('Admin Dashboard') }}
                        </x-nav-link>
                        @endif

                        @if(Auth::user()->hasRole('Tenant'))
                        <x-nav-link :href="route('tenant.products')" :active="request()->routeIs('tenant.products*')" wire:navigate>
                            {{ __('Produk') }}
                        </x-nav-link>
                        <x-nav-link :href="route('tenant.orders')" :active="request()->routeIs('tenant.orders*')" wire:navigate>
                            {{ __('Pesanan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('tenant.report')" :active="request()->routeIs('tenant.report*')" wire:navigate>
                            {{ __('Laporan') }}
                        </x-nav-link>
                        @endif


                        @if(Auth::user()->hasRole('Exhibitor'))
                        <x-nav-link :href="route('exhibitor.dashboard')" :active="request()->routeIs('exhibitor.dashboard')" wire:navigate>
                            {{ __('Exhibitor Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('scanner.page')" :active="request()->routeIs('scanner.page')" wire:navigate>
                            {{ __('QR Scanner') }}
                        </x-nav-link>
                        @endif

                        {{-- Hanya tampilkan menu ini jika user bukan Exhibitor --}}
                        {{-- Tujuannya agar Exhibitor tidak melihat menu Exhibitors di navbar --}}
                        @if(!Auth::user()?->hasRole('Exhibitor'))
                        <!-- <x-nav-link :href="route('exhibitors.index')" :active="request()->routeIs('exhibitors.index')" wire:navigate>
                            {{ __('Exhibitors') }}
                        </x-nav-link> -->
                        @endif

                        {{-- Hanya tampilkan menu ini jika user memiliki permission 'manage events' --}}
                        @can('manage events')
                        <x-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')" wire:navigate>
                            {{ __('nav.events') }}
                        </x-nav-link>
                        @endcan

                        {{-- Hanya Admin & Event Manager yang bisa akses --}}
                        @if(Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Manager'))
                        <x-nav-link :href="route('admin.vouchers.index')" :active="request()->routeIs('admin.vouchers.*')" wire:navigate>
                            {{ __('Vouchers') }}
                        </x-nav-link>
                        @endif

                        @can('manage pages')
                        <x-nav-link :href="route('admin.pages.index')" :active="request()->routeIs('admin.pages.index')" wire:navigate>
                            {{ __('Pages') }}
                        </x-nav-link>
                        @endcan

                        @can('manage welcome')
                        <x-nav-link :href="route('admin.banners.index')" :active="request()->routeIs('admin.banners.*')" wire:navigate>
                            {{ __('Banners') }}
                        </x-nav-link>
                        @endcan

                        @can('manage broadcasts')
                        <x-nav-link :href="route('admin.global-broadcast')" :active="request()->routeIs('admin.global-broadcast')">
                            {{ __('Global Broadcast') }}
                        </x-nav-link>
                        @endcan

                        {{-- Hanya tampilkan menu ini jika user memiliki permission 'manage media' --}}

                        <div class="relative flex" x-data="{ open: false }">
                            @can('manage media')
                            <button @click="open = !open" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.media.*') ? 'border-primary text-white' : 'border-transparent text-gray-300 hover:text-white' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                <span>{{ __('Media Library') }}</span>
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            @endcan

                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="origin-top-left absolute left-0 mt-12 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <x-dropdown-link :href="route('admin.media.index')" :active="request()->routeIs('admin.media.index')" wire:navigate>{{ __('Media Library') }}</x-dropdown-link>
                                    @can('manage social wall')
                                    <x-dropdown-link :href="route('admin.social-wall.index')" :active="request()->routeIs('admin.social-wall.index')" wire:navigate> {{ __('Social Wall') }}</x-dropdown-link>
                                    @endcan
                                    <x-dropdown-link :href="route('admin.files.index')" :active="request()->routeIs('admin.files.index')" wire:navigate>{{ __('Files Manager') }}</x-dropdown-link>
                                </div>
                            </div>
                        </div>

                        {{-- Hanya tampilkan menu ini jika user Admin --}}
                        @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin'))
                        <x-nav-link :href="route('admin.analytics.interest')" :active="request()->routeIs('admin.analytics.interest')" wire:navigate>
                            {{ __('Reports') }}
                        </x-nav-link>
                        @endif


                        {{-- Dropdown untuk News (tidak berubah) --}}
                        <div class="relative flex" x-data="{ open: false }">
                            @can('manage news')
                            <button @click="open = !open" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.news.*') ? 'border-primary text-white' : 'border-transparent text-gray-300 hover:text-white' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                <span>News</span>
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            @endcan

                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="origin-top-left absolute left-0 mt-12 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <x-dropdown-link :href="route('admin.news.index')" :active="request()->routeIs('admin.news.index')" wire:navigate>GreenNews</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.news.categories')" :active="request()->routeIs('admin.news.categories')" wire:navigate>Categories</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.ads.index')" :active="request()->routeIs('admin.ads.index')" wire:navigate> {{ __('Ad Management') }}</x-dropdown-link>
                                </div>
                            </div>
                        </div>



                        {{-- DIUBAH: Menambahkan logika untuk menampilkan dropdown Settings hanya jika user memiliki salah satu permission terkait --}}
                        @if(Auth::user()->can('manage application settings') || Auth::user()->can('manage forms') || Auth::user()->can('manage users') || Auth::user()->can('manage roles_permissions') || Auth::user()->can('manage section templates') || Auth::user()->can('manage menus'))
                        {{-- Dropdown untuk Settings --}}
                        <div class="relative flex" x-data="{ open: false }">

                            <button @click="open = !open" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.settings.*', 'admin.forms.*', 'admin.feedback-forms.*', 'admin.users.*', 'admin.roles.*', 'admin.section-templates.*', 'admin.menus.*') ? 'border-primary text-white' : 'border-transparent text-gray-300 hover:text-white' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                <span>Settings</span>
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="origin-top-left absolute left-0 mt-12 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    @can('manage application settings')
                                    <x-dropdown-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.index')" wire:navigate>Application Settings</x-dropdown-link>
                                    <x-dropdown-link href="{{ route('admin.settings.exhibitor-export') }}" :active="request()->routeIs('admin.settings.exhibitor-export')" wire:navigate>
                                        {{ __('Ekspor Exhibitor') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.settings.sticky-bar')" :active="request()->routeIs('admin.settings.sticky-bar')" wire:navigate>
                                        Sticky Bar Settings
                                    </x-dropdown-link>
                                    @endcan
                                    {{-- Hanya tampilkan menu ini jika user memiliki permission 'manage forms' --}}
                                    @can('manage forms')
                                    <x-dropdown-link :href="route('admin.forms.index')" :active="request()->routeIs('admin.forms.index')" wire:navigate>Custom Forms</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.feedback-forms.index')" :active="request()->routeIs('admin.feedback-forms.index')" wire:navigate>Feedback Forms</x-dropdown-link>
                                    @endcan
                                    {{-- Hanya tampilkan menu ini jika user memiliki permission 'manage users' atau 'manage roles_permissions' --}}
                                    @canany(['manage users', 'manage roles_permissions'])
                                    <div class="border-t border-gray-100"></div>
                                    @endcanany
                                    @can('manage users')
                                    <x-dropdown-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')" wire:navigate>Users</x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.index')" wire:navigate>Roles & Permissions</x-dropdown-link>
                                    @endcan
                                    @can('manage section templates')
                                    <x-dropdown-link :href="route('admin.section-templates.index')" :active="request()->routeIs('admin.section-templates.index')" wire:navigate>
                                        {{ __('Section Templates') }}
                                    </x-dropdown-link>
                                    @endcan

                                    @can('manage menus')
                                    <x-dropdown-link :href="route('admin.menus.index')" :active="request()->routeIs('admin.menus.index')" wire:navigate>
                                        {{ __('Menu Manager') }}
                                    </x-dropdown-link>
                                    @endcan


                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                    @endif

                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-gray-800 hover:text-white focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>


                            <x-slot name="content">
                                {{-- ====================================================== --}}
                                {{-- BARU: Logika untuk menampilkan link yang relevan --}}
                                {{-- ====================================================== --}}
                                @if(Auth::user()->roles->isEmpty())
                                {{-- Untuk Pengguna Biasa (tidak punya role) --}}
                                <x-dropdown-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                                    {{ __('My Dashboard') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('scanner.page')" :active="request()->routeIs('scanner.page')" wire:navigate>
                                    {{ __('QR Scanner') }}
                                </x-dropdown-link>
                                @else
                                @endif

                                @if(Auth::user()->hasRole('Exhibitor'))
                                <x-dropdown-link :href="route('exhibitor.profile')" :active="request()->routeIs('exhibitor.profile')" wire:navigate>
                                    {{ __('My Profile') }}
                                </x-dropdown-link>
                                @else
                                <x-dropdown-link :href="route('profile')" :active="request()->routeIs('profile')" wire:navigate>
                                    {{ __('My Profile') }}
                                </x-dropdown-link>
                                @endif

                                <div class="border-t border-gray-200"></div>

                                <x-dropdown-link wire:click="logout" style="cursor: pointer;">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @else
                    {{-- Menu untuk pengunjung (tidak login) --}}
                    <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white transition-colors duration-150">{{ __('nav.login') }}</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium text-white bg-greener hover:bg-greenerlight px-4 py-2 rounded-md transition-colors duration-150">{{ __('nav.create_account') }}</a>
                    </div>
                    @endguest
                </div>

                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>



        {{-- Responsive View --}}
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-800 border-t border-gray-700">
            @guest
            {{-- Menu Publik untuk Mobile --}}
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')" wire:navigate>{{ __('nav.home') }}</x-responsive-nav-link>

                @foreach($headerMenuItems as $item)
                @if($item->children->isEmpty())
                <x-responsive-nav-link :href="url($item->link)" :active="request()->fullUrlIs(url($item->link))" wire:navigate>
                    {{ $item->label }}
                </x-responsive-nav-link>
                @else
                {{-- Dropdown/accordion untuk mobile --}}
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex justify-between items-center ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition duration-150 ease-in-out">
                        <span>{{ $item->label }}</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="pl-4 space-y-1">
                        @foreach($item->children as $child)
                        <x-responsive-nav-link :href="url($child->link)" :active="request()->fullUrlIs(url($child->link))" wire:navigate class="pl-8 block">
                            {{ $child->label }}
                        </x-responsive-nav-link>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            {{-- Pemisah dan Link Login/Register untuk Mobile --}}
            <div class="pt-4 pb-3 border-t border-gray-700">
                <div class="px-4 space-y-2">
                    <a href="{{ route('login') }}" class="block text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 px-3 py-2 rounded-md transition-colors duration-150">{{ __('Log in') }}</a>
                    <a href="{{ route('register') }}" class="block text-base font-medium text-white bg-greener hover:bg-greenerlight px-3 py-2 rounded-md transition-colors duration-150">{{ __('Register') }}</a>
                </div>
            </div>
            @endguest

            @auth
            <div class="pt-2 pb-3 space-y-1">
                {{-- Menu untuk Pengguna Biasa (tidak punya role) --}}
                @if(Auth::user()->roles->isEmpty())
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>{{ __('nav.my_dashboard') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('user.qrcode')" :active="request()->routeIs('user.qrcode')" wire:navigate>
                    {{ __('My Digital Card') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('scanner.page')" :active="request()->routeIs('scanner.page')" wire:navigate>{{ __('nav.qr_scanner') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('exhibitors.index')" :active="request()->routeIs('exhibitors.index')" wire:navigate>{{ __('nav.exhibitors') }}</x-responsive-nav-link>
                @endif

                {{-- Menu untuk Admin/Pengguna dengan Peran --}}
                @if(Auth::user()->roles->isNotEmpty())
                @if(Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Admin'))
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" wire:navigate>
                    {{ __('Admin Dashboard') }}
                </x-responsive-nav-link>
                @endif

                @if(Auth::user()->hasRole('Exhibitor'))
                <x-responsive-nav-link :href="route('exhibitor.dashboard')" :active="request()->routeIs('exhibitor.dashboard')" wire:navigate>
                    {{ __('Exhibitor Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('scanner.page')" :active="request()->routeIs('scanner.page')" wire:navigate>
                    {{ __('QR Scanner') }}
                </x-responsive-nav-link>
                @endif

                @if(!Auth::user()?->hasRole('Exhibitor'))
                <x-responsive-nav-link :href="route('exhibitors.index')" :active="request()->routeIs('exhibitors.index')" wire:navigate>
                    {{ __('Exhibitors') }}
                </x-responsive-nav-link>
                @endif

                @can('manage events')
                <x-responsive-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')" wire:navigate>
                    {{ __('nav.events') }}
                </x-responsive-nav-link>
                @endcan

                @can('manage pages')
                <x-responsive-nav-link :href="route('admin.pages.index')" :active="request()->routeIs('admin.pages.index')" wire:navigate>
                    {{ __('Pages') }}
                </x-responsive-nav-link>
                @endcan

                @can('manage welcome')
                <x-responsive-nav-link :href="route('admin.banners.index')" :active="request()->routeIs('admin.banners.*')" wire:navigate>
                    {{ __('Banners') }}
                </x-responsive-nav-link>
                @endcan

                @can('manage broadcasts')
                <x-responsive-nav-link :href="route('admin.global-broadcast')" :active="request()->routeIs('admin.global-broadcast')">
                    {{ __('Global Broadcast') }}
                </x-responsive-nav-link>
                @endcan

                @can('manage media')
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex justify-between items-center ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition duration-150 ease-in-out">
                        <span>{{ __('Media Library') }}</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('admin.media.index')" wire:navigate>{{ __('Media Library') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.news.categories')" :active="request()->routeIs('admin.news.categories')" wire:navigate>Categories</x-responsive-nav-link>
                        @can('manage social wall')
                        <x-responsive-nav-link :href="route('admin.social-wall.index')" :active="request()->routeIs('admin.social-wall.index')" wire:navigate> {{ __('Social Wall') }}</x-responsive-nav-link>
                        @endcan
                    </div>
                </div>
                @endcan

                @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin'))
                <x-responsive-nav-link :href="route('admin.analytics.interest')" :active="request()->routeIs('admin.analytics.interest')" wire:navigate>
                    {{ __('Reports') }}
                </x-responsive-nav-link>
                @endif

                @can('manage news')
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex justify-between items-center ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition duration-150 ease-in-out">
                        <span>News</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('admin.news.index')" wire:navigate>GreenNews</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.news.categories')" wire:navigate>Categories</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.ads.index')" :active="request()->routeIs('admin.ads.index')" wire:navigate> {{ __('Ad Management') }}</x-responsive-nav-link>
                    </div>
                </div>
                @endcan

                @if(Auth::user()->can('manage application settings') || Auth::user()->can('manage forms') || Auth::user()->can('manage users') || Auth::user()->can('manage roles_permissions') || Auth::user()->can('manage section templates') || Auth::user()->can('manage menus'))
                <div x-data="{ open: false }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex justify-between items-center ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition duration-150 ease-in-out">
                        <span>Settings</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="pl-4 space-y-1">
                        @can('manage application settings')
                        <x-responsive-nav-link :href="route('admin.settings.index')" wire:navigate>Application Settings</x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('admin.settings.exhibitor-export') }}" :active="request()->routeIs('admin.settings.exhibitor-export')" wire:navigate>
                            {{ __('Ekspor Exhibitor') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.settings.sticky-bar')" wire:navigate>
                            Sticky Bar Settings
                        </x-responsive-nav-link>
                        @endcan
                        @can('manage forms')
                        <x-responsive-nav-link :href="route('admin.forms.index')" wire:navigate>Custom Forms</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.feedback-forms.index')" wire:navigate>Feedback Forms</x-responsive-nav-link>
                        @endcan
                        @canany(['manage users', 'manage roles_permissions'])
                        <div class="border-t border-gray-200"></div>
                        @endcanany
                        @can('manage users')
                        <x-responsive-nav-link :href="route('admin.users.index')" wire:navigate>Users</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.roles.index')" wire:navigate>Roles & Permissions</x-responsive-nav-link>
                        @endcan
                        @can('manage section templates')
                        <x-responsive-nav-link :href="route('admin.section-templates.index')" :active="request()->routeIs('admin.section-templates.index')" wire:navigate>
                            {{ __('Section Templates') }}
                        </x-responsive-nav-link>
                        @endcan
                        @can('manage menus')
                        <x-responsive-nav-link :href="route('admin.menus.index')" wire:navigate>
                            {{ __('Menu Manager') }}
                        </x-responsive-nav-link>
                        @endcan
                    </div>
                </div>
                @endif
                @endif
            </div>
            {{-- Opsi User untuk Mobile --}}
            <div class="pt-4 pb-1 border-t border-gray-700">
                <div class="px-4">
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    @if(Auth::user()->hasRole('Exhibitor'))
                    <x-responsive-nav-link :href="route('exhibitor.profile')" :active="request()->routeIs('exhibitor.profile')" wire:navigate>
                        {{ __('My Profile') }}
                    </x-responsive-nav-link>
                    @else
                    <x-responsive-nav-link :href="route('profile')" :active="request()->routeIs('profile')" wire:navigate>
                        {{ __('My Profile') }}
                    </x-responsive-nav-link>
                    @endif

                    <div class="border-t border-gray-600"></div>

                    <x-responsive-nav-link wire:click="logout" style="cursor: pointer;">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </div>
            </div>
            @endauth
        </div>
    </nav>
</div>