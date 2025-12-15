<div class="min-h-screen flex flex-col justify-center items-center bg-gray-100 p-4">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full text-center">
        <div class="mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Pesanan Dibatalkan</h2>
            <p class="text-gray-600">
                Pesanan Anda untuk event <strong>{{ $registration->event->name }}</strong> telah berhasil dibatalkan.
            </p>
        </div>

        <div class="space-y-3">
            {{-- Tombol Daftar Ulang --}}
            <a href="{{ route('event.register', $registration->event->slug) }}" class="block w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                Daftar Ulang
            </a>

            {{-- Tombol Home --}}
            <a href="{{ route('home') }}" class="block w-full py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>