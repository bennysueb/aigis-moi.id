<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Digital Card') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center flex flex-col items-center">

                    {{-- Bagian Profil Utama --}}
                    <div class="mb-6">
                        <h3 class="text-3xl font-bold text-gray-900">
                            {{ auth()->user()->name }}
                        </h3>

                        {{-- Menampilkan Jabatan dan Instansi jika ada --}}
                        @if (auth()->user()->jabatan && auth()->user()->nama_instansi)
                        <p class="mt-1 text-lg text-gray-600">
                            {{ auth()->user()->jabatan }} at {{ auth()->user()->nama_instansi }}
                        </p>
                        @elseif (auth()->user()->jabatan)
                        <p class="mt-1 text-lg text-gray-600">
                            {{ auth()->user()->jabatan }}
                        </p>
                        @elseif (auth()->user()->nama_instansi)
                        <p class="mt-1 text-lg text-gray-600">
                            {{ auth()->user()->nama_instansi }}
                        </p>
                        @endif
                    </div>

                    {{-- QR Code --}}
                    <div class="mb-8 flex justify-center">
                        {!! $qrCode !!}
                    </div>

                    {{-- Detail Kontak --}}
                    <div class="w-full text-left space-y-4 mb-8">
                        @if(auth()->user()->email)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <span class="text-gray-700">{{ auth()->user()->email }}</span>
                        </div>
                        @endif

                        @if(auth()->user()->phone_number)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                            <span class="text-gray-700">{{ auth()->user()->phone_number }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Tombol Edit Profil --}}
                    <a href="{{ route('profile') }}" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                        Edit Profile
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>