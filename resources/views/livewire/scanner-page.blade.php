<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('QR Code Scanner') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8" x-data="{ isScannerActive: false }">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">

                    {{-- ====================================================== --}}
                    {{-- BARU: Tombol untuk memulai kamera --}}
                    {{-- ====================================================== --}}
                    <div x-show="!isScannerActive">
                        <button @click="isScannerActive = true; startScanner();"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700">
                            <i class="fas fa-camera mr-2"></i>
                            {{ __('scan.open_camera') }}
                        </button>
                    </div>

                    {{-- ====================================================== --}}
                    {{-- Wadah kamera, hanya tampil setelah tombol diklik --}}
                    {{-- ====================================================== --}}
                    <div x-show="isScannerActive" style="display: none;">
                        <div id="qr-reader" style="width:100%"></div>
                        <div id="qr-reader-results" class="mt-4 font-semibold"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let html5QrCode; // Definisikan di scope luar agar bisa diakses

        // Fungsi untuk memulai scanner
        function startScanner() {
            // Inisialisasi hanya saat fungsi dipanggil
            html5QrCode = new Html5Qrcode("qr-reader");
            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            };

            html5QrCode.start({
                    facingMode: "environment"
                },
                config,
                onScanSuccess,
                (errorMessage) => {
                    /* Abaikan error */
                }
            ).catch((err) => {
                console.error(`Unable to start the scanner`, err);
            });
        }

        // Fungsi yang berjalan saat scan berhasil
        const onScanSuccess = (decodedText, decodedResult) => {
            html5QrCode.stop().then(() => {
                // MODIFIKASI: Kirim seluruh URL (decodedText) ke backend, bukan hanya UUID-nya.
                @this.call('processScan', decodedText);
            });
        };

        // Listener untuk menangkap sinyal 'sukses' dari backend
        Livewire.on('scan-successful', (data) => {
            Swal.fire({
                title: 'Connection successful!',
                text: 'You are now connected with ' + (data.exhibitorName || 'Exhibitor'),
                icon: 'success',
                confirmButtonText: 'OK' // Diubah dari 'Scan Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Muat ulang halaman saat OK diklik
                    window.location.reload();
                }
            });
        });

        // Listener untuk menangkap sinyal 'gagal' dari backend
        Livewire.on('scan-failed', (data) => {
            Swal.fire({
                title: 'Connection Failed',
                text: data.error,
                icon: 'error',
                confirmButtonText: 'Try Again'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
        });
    </script>
    @endpush
</div>