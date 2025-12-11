<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Check-in Scanner for: <span class="font-bold">{{ $event->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    {{-- Input untuk Handheld Scanner --}}
                    <form wire:submit.prevent="manualCheckIn" class="mb-4">
                        <label for="manualUuid" class="block text-sm font-medium text-gray-700">Scan with Handheld Scanner</label>
                        <input type="text" wire:model="manualUuid" id="manualUuid" placeholder="Click here and scan..."
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md text-center"
                            autofocus>
                    </form>

                    <div class="text-center text-gray-500 my-2">OR</div>

                    {{-- Scanner Kamera --}}
                    <div id="qr-reader" style="width:100%"></div>

                    {{-- Status Box --}}
                    <div id="status-box" class="mt-4 p-4 rounded-md text-lg font-bold @if(isset($lastScanned['status']))
                        {{ $lastScanned['status'] == 'success' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $lastScanned['status'] == 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $lastScanned['status'] == 'error' ? 'bg-red-100 text-red-800' : '' }}
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
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    // Fungsi untuk memainkan suara notifikasi (tidak berubah)
    function playSound(status) {
        let audioFile = status === 'success' ? '/sounds/success.mp3' : '/sounds/error.mp3';
        new Audio(audioFile).play();
    }

    let isScanning = true;

    // Fungsi yang dipanggil saat scan berhasil (tidak berubah)
    function onScanSuccess(decodedText, decodedResult) {
        // Jika sedang tidak dalam mode scan, abaikan hasilnya
        if (!isScanning) {
            return;
        }

        // SEGERA KUNCI SCANNER
        isScanning = false;

        html5QrCode.stop().then(ignore => {
            console.log("Camera stopped.");
            let uuid = decodedText.split('/').pop();
            @this.call('checkIn', uuid);
        }).catch(err => {
            console.error("Failed to stop the scanner.", err);
            isScanning = true; // Buka kembali kunci jika gagal berhenti
        });
    }

    // Fungsi yang dipanggil saat scan error (tidak berubah)
    function onScanError(errorMessage) {
        // bisa diabaikan
    }

    let html5QrCode;

    document.addEventListener('livewire:init', () => {
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
            onScanError
        ).catch(err => {
            console.error("Tidak dapat memulai kamera.", err);
        });

        // Listener Livewire (tidak berubah)
        Livewire.on('scan-finished', () => {
            setTimeout(() => {
                window.location.href = "{{ route('admin.checkin.camera', $event) }}";
            }, 1500);
        });
        Livewire.on('scan-successful', () => {
            playSound('success');
        });
        Livewire.on('scan-failed', () => {
            playSound('error');
        });
    });
</script>
@endpush