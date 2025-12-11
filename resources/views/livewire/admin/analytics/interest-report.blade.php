<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">
                {{ __('Interest & Engagement Report') }}
            </h1>
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25">

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                {{-- Kartu Statistik Utama --}}
                <div class="mb-8 p-6 bg-white rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-500">Rata-rata Exhibitor Dikunjungi / Peserta</h3>
                    <p class="text-5xl font-bold mt-2">{{ $averageVisitsPerAttendee }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Tabel Exhibitor Paling Banyak Dikunjungi --}}
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Top 10: Paling Banyak Dikunjungi (Scan)</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Instansi</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Scan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($mostVisitedExhibitors as $exhibitor)
                                    <tr>
                                        <td class="px-4 py-3">{{ $exhibitor->nama_instansi }}</td>
                                        <td class="px-4 py-3 font-semibold">{{ $exhibitor->total }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-center text-gray-500">Belum ada data.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tabel Exhibitor Paling Banyak Difavoritkan --}}
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Top 10: Paling Banyak Difavoritkan</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Instansi</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Favorit</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($mostFavoritedExhibitors as $exhibitor)
                                    <tr>
                                        <td class="px-4 py-3">{{ $exhibitor->nama_instansi }}</td>
                                        <td class="px-4 py-3 font-semibold">{{ $exhibitor->total }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-center text-gray-500">Belum ada data.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Paling Banyak Difavoritkan (LOVE) --}}
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Top 10: Paling Banyak Di-"Love" ❤️</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Instansi</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah "Love"</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($mostLovedExhibitors as $exhibitor)
                                    <tr>
                                        <td class="px-4 py-3">{{ $exhibitor->nama_instansi }}</td>
                                        <td class="px-4 py-3 font-semibold">{{ $exhibitor->love_count }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-center text-gray-500">Belum ada data.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Rating Tertinggi --}}
                    <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-2">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Top 10: Rata-rata Rating Tertinggi ⭐</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Instansi</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rata-rata Rating</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Penilai</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($topRatedExhibitors as $exhibitor)
                                    <tr>
                                        <td class="px-4 py-3">{{ $exhibitor->nama_instansi }}</td>
                                        <td class="px-4 py-3 font-semibold">{{ number_format($exhibitor->average_rating, 2) }} / 5</td>
                                        <td class="px-4 py-3">{{ $exhibitor->total_ratings }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-center text-gray-500">Belum ada data.</td>
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
</div>