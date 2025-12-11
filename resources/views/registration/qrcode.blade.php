<x-app-layout>
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-2">Ticket for {{ $registration->event->name }}</h2>
                <p class="text-lg mb-4"><strong>{{ $registration->name }}</strong></p>
                <div class="flex justify-center">
                    {!! $qrCode !!}
                </div>
                <p class="mt-4 text-gray-600">Scan this QR code at the venue for check-in.</p>
            </div>
        </div>
    </div>
</x-app-layout>