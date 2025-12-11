<div class="min-h-screen bg-gray-50 py-8 md:py-12 font-sans text-gray-900 print:bg-white print:py-0">
    
    {{-- STYLE KHUSUS PRINT (A4 PORTRAIT) --}}
    <style>
        @media print {
            @page {
                margin: 0;
                size: A4 portrait; /* Paksa Ukuran Kertas A4 Portrait */
            }
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background-color: white !important;
                width: 210mm; /* Lebar fisik A4 */
                height: 297mm; /* Tinggi fisik A4 */
            }
            /* Hilangkan elemen navigasi/footer default website */
            header, footer, nav, .no-print {
                display: none !important;
            }
            /* Paksa Layout Grid 2 Kolom (Billed To & Detail Event) */
            .print-grid-2 {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 1.5rem !important;
            }
            .print-flex-row {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            /* Reset Container Utama agar pas di kertas */
            .print-container {
                max-width: none !important;
                width: 100% !important;
                padding: 1.5cm !important; /* Margin aman A4 */
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
            /* Header Gelap Tetap Muncul Warnanya */
            .print-header-bg {
                background-color: #0f172a !important; /* Slate-900 */
                color: white !important;
                padding: 1.5rem !important;
                border-radius: 0.5rem !important;
            }
            .print-header-text {
                color: white !important;
            }
            /* Status Bar Rapi */
            .print-status-bar {
                background-color: #f9fafb !important;
                border-bottom: 1px solid #e5e7eb !important;
                padding: 1rem 0 !important;
                margin-bottom: 1.5rem !important;
            }
            /* Tabel Rapi */
            .print-table th {
                background-color: #f3f4f6 !important;
                color: #374151 !important;
            }
            /* Force Text Colors */
            .text-white {
                color: #000 !important; /* Fallback jika background hilang */
            }
            /* Hapus Scrollbar di Tabel */
            .overflow-x-auto {
                overflow: visible !important;
            }
        }
    </style>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 print-container">

        {{-- MAIN INVOICE CARD --}}
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden print:shadow-none print:rounded-none print:border-0">
            
            {{-- HEADER: Brand & Invoice Info --}}
            <div class="bg-secondary-light text-white p-8 md:p-10 flex flex-col md:flex-row justify-between items-start md:items-center print-header-bg print-flex-row">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold tracking-tight uppercase print-header-text">Invoice</h2>
                    <p class="text-slate-400 text-sm mt-1 print-header-text">Terima kasih atas pendaftaran Anda.</p>
                </div>
                <div class="mt-6 md:mt-0 text-left md:text-right">
                    <p class="text-slate-400 text-xs uppercase tracking-wider print-header-text">Invoice Number</p>
                    <p class="text-xl font-mono font-bold print-header-text">{{ substr($registration->transaction->id ?? 'TRX-PENDING', -8) }}</p>
                    <p class="text-slate-400 text-sm mt-1 print-header-text">Issued: {{ $registration->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            {{-- STATUS BAR --}}
            <div class="px-8 md:px-10 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center print-flex-row print-status-bar">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Status Pembayaran</span>
                
                @if($registration->payment_status == 'paid')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        LUNAS (PAID)
                    </span>
                @elseif($registration->payment_status == 'refunded')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gray-100 text-gray-800 border border-gray-200">
                        DIKEMBALIKAN (REFUNDED)
                    </span>
                @elseif($registration->status == 'canceled')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-red-100 text-red-800 border border-red-200">
                        DIBATALKAN
                    </span>
                @elseif($registration->payment_status == 'unpaid')
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        MENUNGGU PEMBAYARAN
                    </span>
                @endif
            </div>

            {{-- BODY: 2 Columns Layout --}}
            <div class="p-8 md:p-10 grid grid-cols-1 md:grid-cols-2 gap-10 print-grid-2 print:p-0 print:mt-4">
                
                {{-- Column 1: Billed To --}}
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Tagihan Kepada (Billed To)</h3>
                    <div class="text-gray-800">
                        <p class="text-xl font-bold mb-1">{{ $registration->name }}</p>
                        <p class="text-sm text-gray-600 mb-1">{{ $registration->email }}</p>
                        <p class="text-sm text-gray-600">{{ $registration->phone_number }}</p>
                    </div>
                </div>

                {{-- Column 2: Event Details --}}
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Detail Acara (Event)</h3>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-5 print:bg-white print:border print:border-gray-200">
                        <p class="font-bold text-gray-900 text-lg mb-2">{{ $registration->event->name }}</p>
                        
                        <div class="flex items-start gap-3 mb-2">
                            <div class="mt-1 text-indigo-500 print:text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="text-sm text-gray-600">{{ $registration->event->start_date->format('d F Y, H:i') }}</span>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="mt-1 text-indigo-500 print:text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="text-sm text-gray-600">{{ $registration->event->venue }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ORDER SUMMARY TABLE --}}
            <div class="px-8 md:px-10 pb-8 md:pb-10 print:px-0 print:mt-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Rincian Pesanan</h3>
                
                <div class="border rounded-lg overflow-x-auto print:overflow-visible print:border">
                    <table class="min-w-full divide-y divide-gray-200 print-table">
                        <thead class="bg-gray-50 print:bg-gray-100">
                            <tr>
                                <th scope="col" class="px-4 md:px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Description
                                </th>
                                <th scope="col" class="px-4 md:px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- Baris Tiket --}}
                            <tr>
                                <td class="px-4 md:px-6 py-4 text-sm text-gray-800">
                                    <span class="block font-semibold">
                                        {{ $registration->ticketTier->name ?? 'Standard Ticket' }}
                                    </span>
                                    <span class="text-xs text-gray-500 hidden sm:inline print:inline">Harga Satuan</span>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-right font-mono">
                                    IDR {{ number_format($registration->ticketTier->price ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>

                            {{-- Baris Diskon --}}
                            @if(($registration->ticketTier->price ?? 0) > $registration->total_price)
                            <tr class="bg-emerald-50/50 print:bg-white">
                                <td class="px-4 md:px-6 py-3 text-sm text-emerald-700 print:text-gray-800">
                                    <span class="flex items-center">
                                        Diskon / Voucher
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-3 whitespace-nowrap text-sm text-emerald-700 print:text-gray-800 text-right font-mono">
                                    - {{ number_format(($registration->ticketTier->price ?? 0) - $registration->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endif

                            {{-- Baris TOTAL --}}
                            <tr class="bg-slate-50 print:bg-gray-50 print:border-t-2 print:border-black">
                                <td class="px-4 md:px-6 py-5 whitespace-nowrap text-right text-sm font-bold text-gray-900 uppercase">
                                    Total
                                </td>
                                <td class="px-4 md:px-6 py-5 whitespace-nowrap text-right text-xl md:text-2xl font-bold text-indigo-600 font-mono print:text-black">
                                    <span class="text-xs md:text-sm text-gray-500 mr-1 font-sans font-normal">IDR</span>
                                    {{ number_format($registration->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ACTION FOOTER (Hidden on Print) --}}
            <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex flex-col md:flex-row-reverse justify-between items-center gap-4 print:hidden no-print">
                
                {{-- Action Buttons --}}
                <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                    @if($registration->status !== 'canceled' && $registration->payment_status === 'unpaid')
                        <button type="button" 
                            onclick="confirmCancel()"
                            class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 hover:text-red-600 transition w-full md:w-auto text-center order-last md:order-first">
                            Batalkan
                        </button>

                        <button id="pay-button" 
                            class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-bold shadow-lg hover:bg-indigo-700 hover:shadow-xl transition transform hover:-translate-y-0.5 w-full md:w-auto text-center flex justify-center items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Bayar Sekarang
                        </button>
                    @endif

                    @if($registration->payment_status === 'paid')
                        <a href="{{ route('tickets.qrcode', $registration->uuid) }}" 
                           class="px-6 py-2.5 rounded-lg bg-emerald-600 text-white font-bold shadow-md hover:bg-emerald-700 transition w-full md:w-auto text-center flex justify-center items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"></path></svg>
                            Download Tiket
                        </a>
                    @endif

                    @if($registration->status === 'canceled')
                        <a href="{{ route('event.register', $registration->event->slug) }}" 
                           class="px-6 py-2.5 rounded-lg bg-gray-800 text-white font-bold shadow-md hover:bg-gray-900 transition w-full md:w-auto text-center">
                            Daftar Ulang
                        </a>
                    @endif
                </div>

                {{-- Print Button --}}
                <button onclick="window.print()" class="text-gray-500 hover:text-gray-800 text-sm font-medium flex items-center transition w-full md:w-auto justify-center md:justify-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak / Simpan PDF
                </button>
            </div>
        </div>

        {{-- Footer Note --}}
        <div class="mt-8 text-center text-gray-400 text-sm print:hidden no-print">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>

    {{-- Script SweetAlert & Logic Redirect --}}
    @if($registration->status !== 'canceled' && $registration->payment_status === 'unpaid')
    <script>
        function confirmCancel() {
            Swal.fire({
                title: 'Batalkan Pesanan?',
                text: "Pesanan ini akan dibatalkan permanen. Anda harus mendaftar ulang jika ingin memesan lagi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#3b82f6',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Kembali',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-lg px-4 py-2',
                    cancelButton: 'rounded-lg px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    @this.call('cancel').then((success) => {
                        if (success) {
                            window.location.href = "{{ route('order.cancelled', $registration->uuid) }}";
                        } else {
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
                    location.reload();
                },
                onError: function(result) {
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