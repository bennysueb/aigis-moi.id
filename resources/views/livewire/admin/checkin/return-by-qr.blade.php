<div x-data="{ open: false }" x-init="$nextTick(() => $refs.manualScannerInput.focus())">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pengembalian RFID via QR Code
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">

                    <form>
                        <label for="manualUuid" class="block text-sm font-medium text-gray-700">Scan QR Code dengan Handheld Scanner</label>
                        <input type="text" wire:model="manualUuid" id="manualUuid" placeholder="Klik di sini dan scan QR code..."
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md text-center"
                            x-ref="manualScannerInput"
                            @blur="if (!open) $refs.manualScannerInput.focus()"
                            @keydown.enter.prevent.stop="$wire.findUserManually()">
                    </form>

                    <div class="text-center text-gray-500 my-4">OR</div>

                    <p class="mb-4 text-gray-600">Scan QR Code Digital Card peserta untuk mengembalikan RFID.</p>
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
                        Silakan scan QR code peserta.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- MODAL KONFIRMASI PENGEMBALIAN --}}
    {{-- ====================================================== --}}
    <div @open-return-modal.window="open = true;"
        @close-return-modal.window="open = false; $nextTick(() => $refs.manualScannerInput.focus());"
        x-show="open"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75"
        style="display: none;">

        <div @click.away="open = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold mb-4 text-center">
                Konfirmasi Pengembalian RFID
            </h3>
            <p class="text-center text-gray-600 mb-6 text-lg">
                Anda yakin ingin mengembalikan kartu RFID milik: <br>
                <span class="font-bold text-indigo-600 text-xl">{{ $selectedUser?->name }}</span>
                <br>
                (No. RFID: <span class="font-mono">{{ $selectedUser?->rfid_tag }}</span>)
            </p>

            {{-- Tidak perlu form, hanya tombol konfirmasi --}}
            <div class="mt-6 flex justify-between items-center space-x-4">
                <button type="button" @click="open = false" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Batal
                </button>
                <button 
                    type="button" 
                    wire:click="confirmReturn" 
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                    class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    <span wire:loading.remove wire:target="confirmReturn">
                        Ya, Kembalikan Kartu
                    </span>
                    <span wire:loading wire:target="confirmReturn">
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ====================================================== --}}
{{-- SCRIPT KAMERA (TIDAK PERLU DIUBAH SAMA SEKALI) --}}
{{-- ====================================================== --}}
@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text-javascript"></script>
<script>
    document.addEventListener('livewire:init', () => {
        // Cek apakah elemen qr-reader ada sebelum inisialisasi
        if (!document.getElementById("qr-reader")) {
            return;
        }

        const html5QrCode = new Html5Qrcode("qr-reader");
        const config = {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            }
        };

        const startScanner = () => {
            if (html5QrCode.isScanning) return;
            html5QrCode.start({
                    facingMode: "environment"
                }, config, onScanSuccess)
                .catch(err => console.error("Tidak dapat memulai kamera.", err));
        };

        const onScanSuccess = (decodedText, decodedResult) => {
            html5QrCode.stop().then(ignore => {
                console.log("Camera stopped for processing.");
                let uuid = decodedText.split('/').pop();
                @this.call('findUserByUuid', uuid);
            }).catch(err => console.error("Gagal menghentikan scanner.", err));
        };

        // Listener ini akan me-restart kamera
        Livewire.on('reset-scanner-view', () => {
            console.log('Scan cycle finished, restarting scanner...');
            setTimeout(() => {
                startScanner();
            }, 200);
        });

        // Mulai scanner saat halaman dimuat
        startScanner();

        // Pastikan scanner berhenti saat meninggalkan halaman
        window.addEventListener('beforeunload', () => {
            if (html5QrCode.isScanning) {
                html5QrCode.stop();
            }
        });
    });
</script>
@endpush