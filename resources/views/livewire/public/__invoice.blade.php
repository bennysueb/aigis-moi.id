<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        {{-- ... (Bagian Header, Event Details, Customer Info, Order Summary TETAP SAMA) ... --}}

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 print:shadow-none print:p-0">
            <div class="border-b border-gray-200 pb-6 mb-6 flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Invoice #{{ substr($registration->transaction->id ?? 'N/A', -8) }}</h1>
                    <p class="text-sm text-gray-500">Issued: {{ $registration->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="text-right flex flex-col items-end gap-2">
                    @if($registration->payment_status == 'paid')
                    <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">LUNAS (PAID)</span>
                    @elseif($registration->status == 'canceled')
                    <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">DIBATALKAN (CANCELED)</span>
                    @elseif($registration->payment_status == 'unpaid')
                    <span class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">BELUM BAYAR (UNPAID)</span>
                    @endif

                    <button onclick="window.print()" class="print:hidden text-sm text-blue-600 hover:underline flex items-center mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download / Cetak Invoice
                    </button>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Event Details</h2>
                <div class="bg-gray-50 p-4 rounded-lg print:bg-white print:border">
                    <p class="font-bold text-gray-900">{{ $registration->event->name }}</p>
                    <p class="text-gray-600 text-sm mt-1"><i class="far fa-calendar mr-2"></i> {{ $registration->event->start_date->format('d M Y H:i') }}</p>
                    <p class="text-gray-600 text-sm mt-1"><i class="fas fa-map-marker-alt mr-2"></i> {{ $registration->event->venue }}</p>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Billed To</h2>
                <p class="text-gray-900 font-medium">{{ $registration->name }}</p>
                <p class="text-gray-600">{{ $registration->email }}</p>
                <p class="text-gray-600">{{ $registration->phone_number }}</p>
            </div>

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-500 text-sm">
                            <th class="py-2">Description</th>
                            <th class="py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <tr>
                            <td class="py-4">
                                Ticket: {{ $registration->ticketTier->name ?? 'Standard Ticket' }}
                            </td>
                            <td class="py-4 text-right">
                                IDR {{ number_format($registration->ticketTier->price ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        @if(($registration->ticketTier->price ?? 0) > $registration->total_price)
                        <tr>
                            <td class="py-2 text-green-600">Discount (Voucher)</td>
                            <td class="py-2 text-right text-green-600">
                                - IDR {{ number_format(($registration->ticketTier->price ?? 0) - $registration->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                        <tr class="border-t border-gray-200 font-bold text-lg">
                            <td class="py-4">Total</td>
                            <td class="py-4 text-right text-primary">
                                IDR {{ number_format($registration->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end gap-4 mt-6 border-t pt-6 print:hidden">
                @if($registration->status !== 'canceled' && $registration->payment_status === 'unpaid')

                {{-- MODIFIKASI: Tombol Cancel menggunakan SweetAlert --}}
                <button type="button"
                    onclick="confirmCancel()"
                    class="px-4 py-2 border border-red-300 text-red-700 rounded-md hover:bg-red-50 transition">
                    Batalkan Pesanan
                </button>

                {{-- Tombol Pay Now --}}
                <button id="pay-button" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-lg transition font-bold">
                    Pay Now
                </button>
                @endif

                @if($registration->payment_status === 'paid')
                <a href="{{ route('tickets.qrcode', $registration->uuid) }}" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 shadow transition">
                    Download Tiket / QR
                </a>
                @endif

                @if($registration->status === 'canceled')
                <a href="{{ route('event.register', $registration->event->slug) }}" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 shadow transition">
                    Daftar Ulang
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Script SweetAlert & Logic Redirect --}}
    @if($registration->status !== 'canceled' && $registration->payment_status === 'unpaid')
    <script>
        function confirmCancel() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Pesanan ini akan dibatalkan. Anda harus mendaftar ulang jika ingin memesan lagi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#3b82f6',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Tidak, Kembali',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan Loading agar user tahu proses sedang berjalan
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Panggil method 'cancel' di Server dan tunggu hasilnya
                    @this.call('cancel').then((success) => {
                        if (success) {
                            // Jika Server membalas "True" (Sukses), Redirect manual via JS
                            window.location.href = "{{ route('order.cancelled', $registration->uuid) }}";
                        } else {
                            // Jika Gagal (misal sudah terbayar saat itu juga)
                            Swal.close();
                        }
                    });
                }
            })
        }
    </script>
    @endif

    {{-- Script Midtrans --}}
    @if($registration->payment_status === 'unpaid' && $registration->status !== 'canceled' && $registration->transaction)
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function() {
            snap.pay('{{ $registration->transaction->snap_token }}', {
                onSuccess: function(result) {
                    window.location.href = "{{ route('events.register.success', ['event' => $registration->event->slug, 'registration' => $registration->uuid]) }}";
                },
                onPending: function(result) {
                    console.log('pending');
                    location.reload();
                },
                onError: function(result) {
                    console.log('error');
                    location.reload();
                },
                onClose: function() {
                    console.log('closed');
                }
            });
        };
    </script>
    @endif
</div>