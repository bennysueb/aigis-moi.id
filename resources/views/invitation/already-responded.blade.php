<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg text-center">

            <div class="mb-4 flex justify-center">
                {{-- Ikon Info --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800 mb-2">Anda Sudah Merespon</h2>

            <p class="text-gray-600 mb-6">
                Terima kasih, Bapak/Ibu <strong>{{ $invitation->name }}</strong>.
                Anda telah memberikan konfirmasi untuk acara <strong>{{ $event->name }}</strong> pada tanggal {{ $invitation->responded_at->format('d M Y, H:i') }}.
            </p>

            <div class="bg-gray-50 p-4 rounded-md text-sm text-left mb-6">
                <p><strong>Status:</strong>
                    @if($invitation->status == 'confirmed')
                    <span class="text-green-600 font-bold">Akan Hadir</span>
                    @elseif($invitation->status == 'represented')
                    <span class="text-blue-600 font-bold">Diwakilkan</span>
                    @else
                    <span class="text-red-600 font-bold">Tidak Hadir</span>
                    @endif
                </p>

                @if($invitation->status == 'represented' && !empty($invitation->representative_data))
                <p class="mt-2"><strong>Wakil:</strong> {{ $invitation->representative_data['name'] ?? '-' }}</p>
                @endif
            </div>

            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</x-guest-layout>