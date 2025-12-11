{{-- Ganti seluruh isi file dengan kode final ini --}}
<div x-data="{ open: false }" x-init="$nextTick(() => $refs.manualScannerInput.focus())">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            RFID Card Registration for: <span class="font-bold">{{ $event->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">

                    <form>
                        <label for="manualUuid" class="block text-sm font-medium text-gray-700">Scan with Handheld Scanner</label>
                        <input type="text" wire:model="manualUuid" id="manualUuid" placeholder="Click here and scan QR code..."
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md text-center"
                            x-ref="manualScannerInput"
                            @blur="if (!open) $refs.manualScannerInput.focus()"
                            @keydown.enter.prevent.stop="$wire.findUserManually()">
                    </form>

                    <div class="text-center text-gray-500 my-4">OR</div>

                    <p class="mb-4 text-gray-600">Scan a participant's Digital Card QR Code to begin.</p>
                    <div id="qr-reader" style="width:100%"></div>

                    <div id="status-box" class="mt-4 p-4 rounded-md text-lg font-bold @if(isset($lastScanned['status']))
                        {{ $lastScanned['status'] == 'success' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $lastScanned['status'] == 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $lastScanned['status'] == 'error' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $lastScanned['status'] == 'info' ? 'bg-blue-100 text-blue-800' : '' }}
                    @endif">
                        @if(isset($lastScanned['message']))
                        {{ $lastScanned['message'] }}
                        @else
                        Please scan a participant's QR code.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div @open-rfid-modal.window="open = true; $nextTick(() => $refs.rfidInput.focus());"
        @close-rfid-modal.window="open = false; $nextTick(() => $refs.manualScannerInput.focus());"
        x-show="open"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75"
        style="display: none;">

        <div @click.away="open = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold mb-4 text-center">
                Associate RFID for: <br>
                <span class="text-indigo-600">{{ $selectedUser?->name }}</span>
            </h3>
            <p class="text-center text-gray-500 mb-4">Tempelkan kartu RFID pada reader sekarang.</p>

            <form>
                <input type="text"
                    wire:model="rfidTag"
                    x-ref="rfidInput"
                    placeholder="Menunggu nomor RFID..."
                    class="mt-1 block w-full shadow-sm sm:text-lg border-gray-300 rounded-md text-center"
                    @keydown.enter.prevent.stop="$wire.associateRfid()">

                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" @click="open = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="button" wire:click="associateRfid" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Associate Card
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('livewire:init', () => {
        const html5QrCode = new Html5Qrcode("qr-reader");
        const config = {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            }
        };

        // Fungsi untuk memulai scanner
        const startScanner = () => {
            // Cek apakah scanner sudah aktif, jika ya, jangan mulai lagi
            if (html5QrCode.isScanning) return;

            html5QrCode.start({
                    facingMode: "environment"
                }, config, onScanSuccess)
                .catch(err => console.error("Tidak dapat memulai kamera.", err));
        };

        // Fungsi yang dijalankan saat scan berhasil
        const onScanSuccess = (decodedText, decodedResult) => {
            // 1. HENTIKAN KAMERA SEGERA setelah berhasil scan untuk mencegah scan berulang
            html5QrCode.stop().then(ignore => {
                console.log("Camera stopped for processing.");

                // Ambil hanya UUID dari hasil scan
                let uuid = decodedText.split('/').pop();

                // Panggil method di komponen Livewire
                @this.call('findUserByUuid', uuid);

            }).catch(err => console.error("Gagal menghentikan scanner.", err));
        };

        // 2. BARU: Listener untuk me-restart kamera setelah modal ditutup
        Livewire.on('reset-scanner-view', () => {
            console.log('Scan cycle finished, restarting scanner...');
            // Beri jeda sedikit sebelum memulai ulang scanner
            setTimeout(() => {
                startScanner();
            }, 200);
        });

        // Mulai scanner saat halaman pertama kali dimuat
        startScanner();
    });
</script>
@endpush