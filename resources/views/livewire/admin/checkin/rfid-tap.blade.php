<div 
    class="flex flex-col md:flex-row min-h-screen"
    x-data="{ 
        resetTimer: null,
        init() { 
            this.refocusInput(); 
            $wire.on('refocus-rfid-input', () => this.refocusInput());
            $wire.on('scan-processed', () => this.startResetTimer());
        },
        refocusInput() {
            setTimeout(() => { $refs.rfidInput.focus(); }, 50);
        },
        startResetTimer() {
            clearTimeout(this.resetTimer);
            this.resetTimer = setTimeout(() => {
                $wire.resetStatus();
            }, 5000); // 5 detik
        }
    }"
    @click="refocusInput()"
>
    <input 
        type="text" 
        class="absolute" 
        style="opacity: 0; pointer-events: none;"
        x-ref="rfidInput"
        wire:model="rfidTag"
        wire:keydown.enter.prevent="checkInByRfid"  
        @keyup.enter.prevent
        autofocus
    >

    <div class="w-full md:w-1/3 bg-gray-800 text-white p-8 md:p-12 flex flex-col justify-center">
        
        <div class="mb-6">
            @if(config('settings.app_logo'))
                {{-- Jika ada, tampilkan gambar logo --}}
                <img class="block h-20 w-auto" src="{{ asset('storage/' . config('settings.app_logo')) }}" alt="{{ config('settings.app_name', 'Logo') }}">
            @else
                {{-- Jika tidak ada, tampilkan teks nama aplikasi --}}
                <h1 class="font-heading text-xl font-bold">{{ config('settings.app_name', 'Registrasi.Events') }}</h1>
            @endif
        </div>

        <h1 class="text-2xl font-medium text-gray-300">RFID Check-in</h1>
        <h2 class="text-4xl lg:text-5xl font-bold mt-2 leading-tight">
            {{ $event->name }}
        </h2>
        <p class="text-xl text-gray-400 mt-6">
            @if (empty($lastStatus))
                Silakan tempelkan kartu RFID pada reader...
            @else
                Pindaian diterima. Memproses...
            @endif
        </p>
    </div>

    <div @class([
        'w-full md:w-2/3 p-8 md:p-12 flex flex-col justify-center items-center text-center transition-all duration-300',
        'bg-green-100' => data_get($lastStatus, 'status') === 'success',
        'bg-yellow-100' => data_get($lastStatus, 'status') === 'warning',
        'bg-red-100' => data_get($lastStatus, 'status') === 'error',
        'bg-gray-100' => empty($lastStatus),
    ])>
        
        @if (empty($lastStatus))
            <svg class="w-32 h-32 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            <h3 class="mt-6 text-3xl font-medium text-gray-400">Menunggu Pindaian...</h3>
        
        @else
            <div>
                @if (data_get($lastStatus, 'status') === 'success')
                    <svg class="w-32 h-32 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                @elseif (data_get($lastStatus, 'status') === 'warning')
                    <svg class="w-32 h-32 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                @elseif (data_get($lastStatus, 'status') === 'error')
                    <svg class="w-32 h-32 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                @endif
            </div>

            <h3 @class([
                'mt-6 text-4xl font-bold',
                'text-green-800' => data_get($lastStatus, 'status') === 'success',
                'text-yellow-800' => data_get($lastStatus, 'status') === 'warning',
                'text-red-800' => data_get($lastStatus, 'status') === 'error',
            ])>
                {{ $lastStatus['message'] }}
            </h3>

            @if(isset($lastStatus['data']))
                <div class="mt-8 text-left w-full max-w-md mx-auto bg-white p-6 rounded-lg shadow-md border">
                    <ul class="divide-y divide-gray-200">
                        @foreach($lastStatus['data'] as $key => $value)
                            <li class="py-3 flex justify-between items-center">
                                <span class="text-base font-medium text-gray-600">{{ $key }}</span>
                                <span class="text-base font-semibold text-gray-900">{{ $value }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        @endif
        
    </div>
</div>