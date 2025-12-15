<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- BAGIAN 1: STAT CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 flex items-center">
                        <div class="bg-blue-500 p-4 rounded-full text-white mr-4"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg></div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Event Aktif & Mendatang</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $activeEventsCount }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 flex items-center">
                        <div class="bg-green-500 p-4 rounded-full text-white mr-4"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg></div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Pendaftar (Event Aktif)</p>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalRegistrantsCount) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 flex items-center">
                        <div class="bg-purple-500 p-4 rounded-full text-white mr-4"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2" />
                            </svg></div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pengguna</p>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalUsersCount) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 2: GRAFIK --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pendaftaran 30 Hari Terakhir</h3>
                        {{-- Panggil fungsi JS kita menggunakan Alpine.js --}}
                        <div x-data x-init="initRegistrationChart()">
                            <canvas id="registrationChart"
                                data-labels="{{ json_encode($registrationChartData['labels']) }}"
                                data-data="{{ json_encode($registrationChartData['data']) }}">
                            </canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">5 Event Terpopuler</h3>
                        {{-- Panggil fungsi JS kita menggunakan Alpine.js --}}
                        <div x-data x-init="initPopularEventsChart()">
                            <canvas id="popularEventsChart"
                                data-labels="{{ json_encode($popularEventsData['labels']) }}"
                                data-data="{{ json_encode($popularEventsData['data']) }}">
                            </canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 3: TABEL PERFORMA EVENT --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Performa Event (Aktif & Mendatang)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendaftar / Kuota</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat Keterisian</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($eventPerformanceData as $event)
                                @php
                                $fillPercentage = ($event->quota > 0) ? ($event->registrations_count / $event->quota) * 100 : 0;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $event->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $event->start_date->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $event->registrations_count }} / {{ $event->quota > 0 ? $event->quota : '∞' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min($fillPercentage, 100) }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ round($fillPercentage) }}%</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($event->status == 'upcoming')<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Mendatang</span>
                                        @elseif($event->status == 'active')<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                        @else<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($event->status) }}</span>@endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada event aktif atau mendatang.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 4: TABEL AKTIVITAS TERBARU --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Aktivitas Pendaftaran Terbaru</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pendaftar</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($recentRegistrations as $registration)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{-- Coba tampilkan nama dari relasi user, jika tidak ada, tampilkan nama dari tabel registration --}}
                                        {{ $registration->user->name ?? $registration->name }}

                                        {{-- Tambahkan label 'Guest' jika tidak ada relasi user --}}
                                        @if(!$registration->user)
                                        <span class="text-xs text-gray-500">(Guest)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $registration->event->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $registration->created_at->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada pendaftaran baru.</td>
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