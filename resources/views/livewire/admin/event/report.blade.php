<div>
    {{-- Slot Header untuk Judul Halaman --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Laporan Kehadiran: <span class="font-bold">{{ $event->getTranslation('name', 'en') }}</span>
            </h2>
            <a href="{{ route('admin.events.index') }}"
                wire:navigate
                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-bold text-xs rounded-md uppercase tracking-wider">
                &larr; Kembali ke Daftar Event
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Statistik Undangan (Invitation System)
                </h3>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

                    {{-- 1. Total Undangan --}}
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-start">
                            <div class="text-center flex-grow">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Data</div>
                                <div class="mt-1 text-2xl font-bold text-gray-800">{{ $invitationStats['total'] }}</div>
                            </div>

                            {{-- Tombol Download All --}}
                            @if($invitationStats['total'] > 0)
                            <button wire:click="exportAll" wire:loading.attr="disabled" class="ml-2 text-gray-600 hover:text-gray-800 p-1 bg-white rounded shadow-sm border border-gray-300" title="Download Rekap Semua Undangan">
                                <svg wire:loading.remove wire:target="exportAll" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <svg wire:loading wire:target="exportAll" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </div>

                    {{-- 2. Terkirim --}}
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                        <div class="text-xs font-medium text-gray-500 uppercase">Terkirim (WA/Email)</div>
                        <div class="mt-1 text-2xl font-bold text-blue-600">{{ $invitationStats['sent'] }}</div>
                        <div class="text-[10px] text-gray-400">
                            {{ $invitationStats['total'] > 0 ? round(($invitationStats['sent'] / $invitationStats['total']) * 100) : 0 }}% coverage
                        </div>
                    </div>

                    {{-- 3. Hadir --}}
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
                        <div class="text-xs font-medium text-gray-500 uppercase">Akan Hadir</div>
                        <div class="mt-1 text-2xl font-bold text-green-600">{{ $invitationStats['confirmed'] }}</div>
                    </div>

                    {{-- 4. Diwakilkan --}}
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-400">
                        <div class="text-xs font-medium text-gray-500 uppercase">Diwakilkan</div>
                        <div class="mt-1 text-2xl font-bold text-blue-500">{{ $invitationStats['represented'] }}</div>
                    </div>

                    {{-- 5. Menolak --}}
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-red-500">
                        <div class="text-xs font-medium text-gray-500 uppercase">Menolak</div>
                        <div class="mt-1 text-2xl font-bold text-red-600">{{ $invitationStats['declined'] }}</div>
                    </div>

                    {{-- 6. Belum Respon & Download --}}
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-orange-200 bg-orange-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-xs font-medium text-orange-800 uppercase">Belum Respon</div>
                                <div class="mt-1 text-2xl font-bold text-orange-600">{{ $invitationStats['pending'] }}</div>
                            </div>

                            {{-- Tombol Download Laporan Pending --}}
                            @if($invitationStats['pending'] > 0)
                            <button wire:click="exportPending" wire:loading.attr="disabled" class="text-orange-600 hover:text-orange-800 p-1 bg-white rounded shadow-sm border border-orange-200" title="Download Data Pending untuk Follow-up">
                                <svg wire:loading.remove wire:target="exportPending" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <svg wire:loading wire:target="exportPending" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                            @endif
                        </div>

                        <div class="text-[10px] text-orange-700 mt-2 font-medium">
                            Response Rate: {{ $invitationStats['response_rate'] }}%
                        </div>
                    </div>

                </div>
            </div>

            {{-- 1. Kartu Statistik Utama (Total & Unik) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                {{-- Kartu Total Pendaftar --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                            Total Pendaftar
                        </h3>
                        <p class="mt-1 text-4xl font-semibold text-gray-900">
                            {{ number_format($totalRegistrations) }}
                        </p>
                    </div>
                </div>

                {{-- Kartu Total Hadir (Unik) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">
                            Total Hadir (Unik)
                        </h3>
                        <p class="mt-1 text-4xl font-semibold text-green-600">
                            {{ number_format($uniqueAttendees) }}
                        </p>
                        @if($totalRegistrations > 0)
                        <span class="text-sm text-gray-500">
                            ({{ round(($uniqueAttendees / $totalRegistrations) * 100, 1) }}% Show-up Rate)
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. Kartu Rincian Harian --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">
                        Rincian Check-in Harian
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jumlah Check-in
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($dailyBreakdown as $day)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{-- Format tanggal agar mudah dibaca (cth: 07 Nov 2025) --}}
                                        {{ \Carbon\Carbon::parse($day->checkin_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ number_format($day->count) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        Belum ada data check-in untuk event ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>