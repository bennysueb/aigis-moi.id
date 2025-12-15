<div
    class="flex items-center justify-center min-h-screen pt-16 bg-gray-100 dark:bg-gray-900"
    x-data="{ 
        resetTimer: null,

        init() { 
            this.refocusInput(); 
            $wire.on('refocus-input', () => this.refocusInput());
            
            // Listener untuk event 'scan-processed' dari Livewire
            $wire.on('scan-processed', () => this.startResetTimer());
        },
        
        refocusInput() {
            // Hanya fokus jika tidak ada elemen modal atau interaktif lain yang aktif
            if (document.activeElement.tagName.toLowerCase() !== 'button') {
                setTimeout(() => { $refs.rfidInput.focus(); }, 50);
            }
        },

        // Fungsi untuk memulai timer reset
        startResetTimer() {
            clearTimeout(this.resetTimer); // Hapus timer sebelumnya (jika ada)
            this.resetTimer = setTimeout(() => {
                $wire.resetStatus(); // Panggil method resetStatus di Livewire
            }, 5000); // 5000ms = 5 detik
        }
    }"
    @click="refocusInput()">
    <input
        type="text"
        class="absolute"
        style="opacity: 0; pointer-events: none;"
        x-ref="rfidInput"
        wire:model="rfidInput"
        wire:keydown.enter.prevent="processReturn"
        @keyup.enter.prevent
        autofocus>

    <div class="relative w-full max-w-lg p-8 m-4 text-center rounded-lg dark:bg-gray-800">

        <div class="mb-6">
            @if ($statusType === 'success')
            <svg class="w-24 h-24 mx-auto text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            @elseif ($statusType === 'error')
            <svg class="w-24 h-24 mx-auto text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            @else
            <svg class="w-24 h-24 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            @endif
        </div>

        <p @class([ 'text-4xl font-extrabold tracking-wider' , 'text-green-700 dark:text-green-300'=> $statusType === 'success',
            'text-red-700 dark:text-red-300' => $statusType === 'error',
            'text-gray-700 dark:text-gray-300' => $statusType === 'neutral',
            ])>
            {{ $statusMessage }}
        </p>

        @if ($lastReturnedName)
        <p class="mt-2 text-2xl font-medium text-gray-600 dark:text-gray-400">
            {{ $lastReturnedName }}
        </p>
        @endif
    </div>

    <div
        class="absolute bottom-10 right-10"
        @click.stop>
        <button
            type="button"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"

            {{-- Ini adalah konfirmasi bawaan browser yang aman --}}
            wire:click="resetAllTags"
            wire:confirm="ANDA YAKIN?
Aksi ini akan mengembalikan SEMUA RFID tag di database menjadi KOSONG (NULL). 
Aksi ini tidak bisa dibatalkan."

            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-wait">
            <span wire:loading.remove wire:target="resetAllTags">
                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Reset Semua RFID
            </span>
            <span wire:loading wire:target="resetAllTags">
                <svg class="inline w-4 h-4 mr-1 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            </span>
        </button>
    </div>
</div>