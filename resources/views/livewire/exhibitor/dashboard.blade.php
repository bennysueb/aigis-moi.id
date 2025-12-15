<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Exhibitor Dashboard') }}
            </h2>
            {{-- Tombol untuk memulai tur kita pindahkan ke header agar lebih rapi --}}
            <x-secondary-button onclick="startTour(true)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Mulai Tur
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            {{-- Menggunakan Grid Layout untuk 2 Kolom --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- KOLOM KIRI (Profil & QR Code) --}}
                <div class="lg:col-span-1 space-y-8">
                    {{-- KARTU PROFIL --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" id="tour-profile">
                        <div class="p-6">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-gray-600 mt-1">{{ __('exhibitor.login_as_representative', ['institution' => $user->nama_instansi]) }}</p>
                            <a href="{{ route('exhibitor.profile') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:text-indigo-800 font-semibold">
                                Lengkapi Profil &rarr;
                            </a>
                        </div>
                    </div>

                    {{-- KARTU QR CODE --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" id="tour-qr-code">
                        <div class="p-6 text-center">
                            <h4 class="text-lg font-semibold">{{ __('exhibitor.booth_qr_code') }}</h4>
                            <p class="text-sm text-gray-500 mb-4">{{ __('exhibitor.scan_instruction') }}</p>
                            <div class="flex justify-center p-2 border rounded-lg bg-white">
                                {!! QrCode::size(250)->generate(route('scan.connect', ['uuid' => $user->uuid])) !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN (Daftar Peserta) --}}
                <div class="lg:col-span-2" id="tour-attendee-list">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xl font-bold">{{ __('exhibitor.attendee_list') }}</h4>
                                @if($attendees->total() > 0)
                                <x-secondary-button wire:click="export" wire:loading.attr="disabled" id="tour-export-button">
                                    <svg wire:loading.remove wire:target="export" class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    <span wire:loading.remove wire:target="export">Export to Excel</span>
                                    <span wire:loading wire:target="export">Exporting...</span>
                                </x-secondary-button>
                                @endif
                            </div>

                            {{-- Tabel Daftar Peserta --}}
                            <div class="overflow-x-auto rounded-lg border">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('exhibitor.name') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('exhibitor.contact') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($attendees as $attendee)
                                        <tr wire:key="{{ $attendee->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $attendee->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $attendee->email }}</div>
                                                <div class="text-sm text-gray-500">{{ $attendee->phone_number }}</div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                                {{ __('exhibitor.no_attendees') }}
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $attendees->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ====================================================== --}}
{{-- ==> SCRIPT UNTUK ONBOARDING TOUR <== --}}
{{-- ====================================================== --}}
@push('scripts')
<script>
    function startTour(force = false) {
        // Cek jika tur sudah pernah dilihat, jangan mulai otomatis
        if (localStorage.getItem('exhibitorDashboardTourCompleted') && !force) {
            return;
        }

        const tour = new Shepherd.Tour({
            // HAPUS OPSI useModalOverlay untuk menghilangkan latar belakang hitam
            // useModalOverlay: true, 
            defaultStepOptions: {
                classes: 'shepherd-custom', // Kita gunakan class kustom
                scrollTo: {
                    behavior: 'smooth',
                    block: 'center'
                }
            }
        });

        tour.addStep({
            id: 'step-1',
            title: 'Kode QR Unik Anda',
            text: 'Ini adalah kode QR untuk booth Anda. Minta pengunjung untuk memindainya agar terhubung.',
            // Diperbarui: Pastikan elemen #tour-qr-code ada sebelum attach
            attachTo: {
                element: '#tour-qr-code',
                on: 'left'
            },
            buttons: [{
                text: 'Lanjut',
                action: tour.next
            }]
        });

        tour.addStep({
            id: 'step-2',
            title: 'Daftar Koneksi Anda',
            text: 'Semua pengunjung yang berhasil memindai QR Anda akan muncul di tabel ini.',
            attachTo: {
                element: '#tour-attendee-list',
                on: 'left'
            },
            buttons: [{
                    text: 'Kembali',
                    secondary: true,
                    action: tour.back
                },
                {
                    text: 'Lanjut',
                    action: tour.next
                }
            ]
        });

        // Hanya tambahkan langkah ini jika tombol ekspor ada di halaman
        if (document.getElementById('tour-export-button')) {
            tour.addStep({
                id: 'step-3',
                title: 'Ekspor Data',
                text: 'Gunakan tombol ini untuk mengunduh daftar semua koneksi Anda ke dalam file Excel.',
                attachTo: {
                    element: '#tour-export-button',
                    on: 'bottom'
                },
                buttons: [{
                        text: 'Kembali',
                        secondary: true,
                        action: tour.back
                    },
                    {
                        text: 'Selesai!',
                        action: tour.complete
                    }
                ]
            });
        }

        // Saat tur selesai, tandai di localStorage
        tour.on('complete', () => {
            localStorage.setItem('exhibitorDashboardTourCompleted', 'true');
        });

        // Saat tur dibatalkan, tandai juga
        tour.on('cancel', () => {
            localStorage.setItem('exhibitorDashboardTourCompleted', 'true');
        });

        tour.start();
    }

    // Memulai tur secara otomatis hanya pada kunjungan pertama
    document.addEventListener('DOMContentLoaded', () => {
        startTour(false);
    });
</script>
@endpush