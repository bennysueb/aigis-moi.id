<div>
    {{-- Cek: Apakah event ini butuh akun DAN user adalah tamu (belum login)? --}}
    @if($event->requires_account && !auth()->check())

    {{-- JIKA YA: Tampilkan "gerbang" pemberitahuan untuk login/register --}}
    <div class="border-l-4 border-blue-400 bg-blue-50 p-6 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-blue-800">Please Log In to Join Event</h3>
                <div class="mt-2 text-sm text-blue-700 space-y-3">
                    <p>This event requires to create account for Join Event. You can log in to proceed, or create account if you don't have one.</p>
                    <p><strong class="font-semibold">Already have an account?</strong> You can also find this event and register easily through your User Dashboard.</p>
                </div>
                <div class="mt-4 flex space-x-4">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Visitor Registration
                    </a>
                </div>
            </div>
        </div>
    </div>

    @else

    {{-- FORM UTAMA --}}
    <div x-data="{
    tipe_instansi: @entangle('formData.tipe_instansi').live,
    signaturePad: null,
    initSignaturePad() {
        const canvas = document.querySelector('#signature-canvas');
        if (canvas) {
            this.signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });
            this.signaturePad.addEventListener('endStroke', () => {
                @this.set('formData.tanda_tangan', this.signaturePad.toDataURL());
            });
        }
    },
    clearSignature() {
        if(this.signaturePad) {
            this.signaturePad.clear();
            @this.set('formData.tanda_tangan', '');
        }
    }
}">
        @if(session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p class="font-bold">Error</p>
            <p>{{ session('error') }}</p>
        </div>
        @elseif($event->quota > 0 && $event->remaining_quota <= 0)
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
            <p class="font-bold">Sold Out</p>
            <p>Sorry, the quota for this event is full.</p>
    </div>
    @else
    <form wire:submit.prevent="register" x-init="initSignaturePad()">
        <div class="space-y-6">

            @if(!empty($event->external_registration_link))

            {{-- LOGIKA LINK EKSTERNAL --}}
            <a href="{{ $event->external_registration_link }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full md:w-auto shadow-lg transition-all transform hover:scale-105">

                <span>Daftar Sekarang (Via Eksternal)</span>

                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>

            @else
            {{-- ============================================================ --}}
            {{-- BAGIAN BARU: TIKET & PEMBAYARAN (Jika Event Berbayar) --}}
            {{-- ============================================================ --}}
            @if($event->is_paid_event)
            <div class="mt-6 border-t pt-6 border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Pilihan Tiket</h3>

                {{-- List Tiket --}}
                <div class="space-y-3">
                    @foreach($event->ticketTiers as $tier)
                    <label class="relative flex flex-col p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $selectedTierId == $tier->id ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50' : 'border-gray-300' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input type="radio" wire:model.live="selectedTierId" value="{{ $tier->id }}" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">{{ $tier->name }}</span>
                                    @if($tier->description)
                                    <span class="block text-sm text-gray-500">{{ $tier->description }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-sm font-bold text-blue-600">
                                Rp {{ number_format($tier->price, 0, ',', '.') }}
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('selectedTierId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                {{-- Voucher Input --}}
                <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Voucher (Opsional)</label>
                    <div class="flex gap-2">
                        <input type="text" wire:model="voucherCode" class="flex-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Masukkan kode promo...">
                        @if($voucherApplied)
                        <button type="button" wire:click="removeVoucher" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none">Hapus</button>
                        @else
                        <button type="button" wire:click="applyVoucher" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 focus:outline-none">Pakai</button>
                        @endif
                    </div>
                    <x-input-error :messages="$errors->get('voucherCode')" class="mt-2" />

                    @if($voucherApplied)
                    <div class="flex items-center mt-2 text-green-600 text-sm font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Voucher {{ $voucherApplied->code }} berhasil digunakan!
                    </div>
                    @endif
                </div>

                {{-- Ringkasan Pembayaran --}}
                <div class="mt-6 bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">Ringkasan Pembayaran</h4>
                    <div class="flex justify-between mb-2 text-sm text-gray-600">
                        <span>Harga Tiket</span>
                        <span>Rp {{ number_format($summary['price'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between mb-2 text-sm text-green-600">
                        <span>Diskon</span>
                        <span>- Rp {{ number_format($summary['discount'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between items-center">
                        <span class="text-base font-bold text-gray-900">Total Bayar</span>
                        <span class="text-xl font-bold text-blue-600">Rp {{ number_format($summary['total'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endif
            {{-- ============================================================ --}}

            {{-- Pilihan Kehadiran untuk Event Hybrid --}}
            @if ($event->type === 'hybrid')
            <div class="p-4 border rounded-md bg-blue-50">
                <label class="block font-medium text-sm text-gray-900">Pilih Tipe Kehadiran <span class="text-red-500">*</span></label>
                <div class="flex items-center space-x-4 mt-2">
                    <label class="flex items-center">
                        <input type="radio" wire:model.live="attendance_type" value="offline" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2">Hadir Secara Offline</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" wire:model.live="attendance_type" value="online" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2">Gabung Secara Online</span>
                    </label>
                </div>
                @error('attendance_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @endif

            {{-- FIELD DATA DIRI STANDAR --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name <span class="text-red-500">*</span></label>
                <input type="text" wire:model.defer="name" id="name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                <input type="email" wire:model.defer="email" id="email" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="tel" wire:model.defer="phone_number" id="phone_number" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                @error('phone_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- FIELD DINAMIS (CUSTOM FIELDS) --}}
            @if(!empty($combinedFields))
            <hr class="my-4">
            @foreach($combinedFields as $field)
            <div>
                <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700">
                    {{ $field['label'] }} @if($field['required']) <span class="text-red-500">*</span> @endif
                </label>

                @if($field['type'] === 'textarea')
                <textarea wire:model.defer="formData.{{ $field['name'] }}" id="{{ $field['name'] }}" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>

                @elseif($field['type'] === 'select')
                <select wire:model.live="formData.{{ $field['name'] }}" id="{{ $field['name'] }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <option value="">-- select an option --</option>
                    @foreach($field['options'] as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>

                {{-- Input 'Others' --}}
                <div x-show="tipe_instansi === 'Others'" x-transition>
                    <label for="tipe_instansi_other" class="block text-sm font-medium text-gray-700 mt-2">Sebutkan Tipe Instansi Lainnya</label>
                    <input type="text" wire:model.defer="formData.tipe_instansi_other" id="tipe_instansi_other" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    @error('formData.tipe_instansi_other') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                @elseif($field['type'] === 'radio')
                <div class="mt-2 space-y-2">
                    @foreach($field['options'] as $option)
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model.defer="formData.{{ $field['name'] }}" value="{{ $option }}"
                            class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm">{{ $option }}</span>
                    </label>
                    @endforeach
                </div>

                @elseif($field['type'] === 'checkbox')
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.defer="formData.{{ $field['name'] }}" value="1"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    </label>
                </div>

                @elseif($field['type'] === 'signature')
                <div wire:ignore class="mt-1">
                    <div class="border border-gray-300 rounded-md">
                        <canvas id="signature-canvas" class="w-full h-32"></canvas>
                    </div>
                    <button type="button" @click="clearSignature()" class="text-xs text-gray-600 hover:text-gray-900 mt-1">Clear Signature</button>
                </div>

                @else
                <input type="{{ $field['type'] }}" wire:model.defer="formData.{{ $field['name'] }}" id="{{ $field['name'] }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                @endif

                @error('formData.' . $field['name']) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @endforeach
            @endif

            <div class="mt-6">
                <button type="submit"
                    class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-greener hover:bg-accent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-75 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                    wire:loading.class="bg-accent"
                    wire:target="register">

                    <svg wire:loading wire:target="register" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>

                    <span wire:loading.remove wire:target="register">
                        {{-- Ubah Teks Tombol Sesuai Status Bayar --}}
                        @if($event->is_paid_event && ($summary['total'] ?? 0) > 0)
                        Bayar & Daftar (Rp {{ number_format($summary['total'], 0, ',', '.') }})
                        @else
                        Register Now
                        @endif
                    </span>
                    <span wire:loading wire:target="register">
                        Processing...
                    </span>
                </button>
            </div>

            @endif
        </div>
    </form>
    @endif
</div>

{{-- SCRIPT HANDLER UNTUK MIDTRANS & SWEETALERT --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        // Listener untuk memulai pembayaran
        @this.on('start-payment', (event) => {
            const snapToken = event.token;
            if (window.snap) {
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        window.location.href = "{{ route('event.registration.success', $event->slug) }}";
                    },
                    onPending: function(result) {
                        Swal.fire('Pending', 'Silakan selesaikan pembayaran Anda.', 'info').then(() => location.reload());
                    },
                    onError: function(result) {
                        Swal.fire('Gagal', 'Pembayaran gagal.', 'error').then(() => location.reload());
                    },
                    onClose: function() {
                        Swal.fire('Dibatalkan', 'Anda menutup popup tanpa membayar.', 'warning');
                    }
                });
            } else {
                console.error('Midtrans Snap JS not loaded');
                alert('Error: Payment gateway not loaded properly.');
            }
        });

        // Listener untuk Notifikasi SweetAlert
        @this.on('swal:success', (event) => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: event.message,
                timer: 2000,
                showConfirmButton: false
            });
        });

        @this.on('swal:error', (event) => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: event.message,
            });
        });
    });
</script>

@endif
</div>