<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <h2 class="text-3xl font-extrabold text-gray-900">Check-in Berhasil!</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Terima kasih telah mengkonfirmasi kehadiran Anda untuk event:<br>
                </p>

                <div class="mt-8 p-6 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-800">Nama Event</h3>
                    <strong>{{ $registration->event->name }}</strong>
                    <a href="{{ $registration->event->meeting_link }}" target="_blank"
                        class="mt-4 inline-block bg-green-600 text-white font-bold py-3 px-8 rounded-lg text-base hover:bg-green-700 transition-colors">
                        Join Meeting Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>