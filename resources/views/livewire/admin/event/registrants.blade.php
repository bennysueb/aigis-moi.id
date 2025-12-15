<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrants for: <span class="font-bold">{{ $event->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Pesan Sukses --}}
            @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
            @endif

            {{-- Form Email Broadcast --}}
            <div class="bg-white overflow-x-scroll shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Send Email Broadcast</h3>

                    @if($templates->isNotEmpty())
                    <div class="mb-4 pb-4 border-b">
                        <p class="text-sm font-medium text-gray-700">Load a Template:</p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($templates as $template)
                            <div class="flex items-center bg-gray-200 rounded-full text-sm">
                                <button type="button" wire:click="loadTemplate({{ $template->id }})"
                                    class="px-3 py-1 text-gray-800 hover:bg-gray-300 rounded-l-full transition-colors">
                                    {{ $template->subject }}
                                </button>
                                <button type="button" wire:click="deleteTemplate({{ $template->id }})"
                                    wire:confirm="Are you sure you want to delete the '{{ $template->subject }}' template?"
                                    class="px-2 py-1 text-red-600 hover:bg-red-200 hover:text-red-800 rounded-r-full transition-colors">
                                    &times;
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- BARU: Notifikasi jumlah pendaftar yang dipilih --}}
                    @if(count($selectedRegistrants) > 0)
                    <div class="bg-blue-100 text-blue-800 p-3 rounded text-sm mb-4 border border-blue-200">
                        {{ count($selectedRegistrants) }} registrant(s) selected.
                    </div>
                    @endif

                    {{-- Notifikasi sukses dari session --}}
                    @if (session()->has('message'))
                    <div class="bg-green-100 text-green-800 p-3 rounded text-sm mb-4 border border-green-200">
                        {{ session('message') }}
                    </div>
                    @endif

                    {{-- Form broadcast tidak lagi menggunakan form submit, tapi wire:click --}}
                    <div class="space-y-4">


                        <div>
                            <label for="broadcastSubject" class="block text-sm font-medium text-gray-700">Subject</label>
                            <input type="text" wire:model.defer="broadcastSubject" id="broadcastSubject" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('broadcastSubject') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>


                        <div>
                            <label for="broadcastContent" class="block text-sm font-medium text-gray-700">Message</label>
                            <x-ckeditor wire:model.defer="broadcastContent" id="broadcastContent"></x-ckeditor>
                            @error('broadcastContent') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-2 text-xs text-gray-500 bg-gray-50 p-3 rounded-md border">
                            <p class="font-semibold mb-2">You can use these placeholders (click to copy):</p>

                            {{-- PERUBAHAN 1: Menambah kelas 'space-y-2' untuk memberi jarak --}}
                            <ul class="list-none space-y-2">
                                <li>
                                    <code class="copyable-placeholder cursor-pointer bg-gray-200 text-gray-800 rounded px-1 py-0.5">[nama_peserta]</code>
                                    <span class="copy-feedback text-green-600 font-semibold ml-2 hidden">Copied!</span>
                                    <span class="text-gray-600">- Participant's name</span>
                                </li>
                                <li>
                                    <code class="copyable-placeholder cursor-pointer bg-gray-200 text-gray-800 rounded px-1 py-0.5">[nama_event]</code>
                                    <span class="copy-feedback text-green-600 font-semibold ml-2 hidden">Copied!</span>
                                    <span class="text-gray-600">- Event's name</span>
                                </li>
                                <li>
                                    <code class="copyable-placeholder cursor-pointer bg-gray-200 text-gray-800 rounded px-1 py-0.5">[link_event_detail]</code>
                                    <span class="copy-feedback text-green-600 font-semibold ml-2 hidden">Copied!</span>
                                    <span class="text-gray-600">- Link to the event detail page</span>
                                </li>
                                <li>
                                    <code class="copyable-placeholder cursor-pointer bg-gray-200 text-gray-800 rounded px-1 py-0.5">[nama_instansi]</code>
                                    <span class="copy-feedback text-green-600 font-semibold ml-2 hidden">Copied!</span>
                                    <span class="text-gray-600">- Participant's institution name</span>
                                </li>
                                <li>
                                    <code class="copyable-placeholder cursor-pointer bg-gray-200 text-gray-800 rounded px-1 py-0.5">[jabatan]</code>
                                    <span class="copy-feedback text-green-600 font-semibold ml-2 hidden">Copied!</span>
                                    <span class="text-gray-600">- Participant's job title</span>
                                </li>
                                <li>
                                    <code class="copyable-placeholder cursor-pointer bg-gray-200 text-gray-800 rounded px-1 py-0.5">[tombol_aksi]</code>
                                    <span class="copy-feedback text-green-600 font-semibold ml-2 hidden">Copied!</span>
                                    <span class="text-gray-600">- Smart button (E-Ticket / Join Online)</span>
                                </li>

                                <li>
                                    <code class="copyable-placeholder cursor-pointer bg-gray-200 text-gray-800 rounded px-1 py-0.5">[tombol_aksi]</code>
                                    <span class="copy-feedback text-green-600 font-semibold ml-2 hidden">Copied!</span>
                                    <span class="text-gray-600">- Smart button (E-Ticket / Join Online)</span>
                                </li>

                                <li>
                                    <code class="copyable-placeholder cursor-pointer bg-gray-200 text-gray-800 rounded px-1 py-0.5">[link_sertifikat]</code>
                                    <span class="copy-feedback text-green-600 font-semibold ml-2 hidden">Copied!</span>
                                    <span class="text-gray-600">- Certificate download link (Admin only)</span>
                                </li>
                                <li>
                                    <code class="copyable-placeholder cursor-pointer bg-gray-200 text-gray-800 rounded px-1 py-0.5">[link_e_tiket]</code>
                                    <span class="copy-feedback text-green-600 font-semibold ml-2 hidden">Copied!</span>
                                    <span class="text-gray-600">- E-Ticket (QR Code) link</span>
                                </li>
                                <li>
                                    <code class="copyable-placeholder cursor-pointer bg-gray-200 text-gray-800 rounded px-1 py-0.5">[link_check_in]</code>
                                    <span class="copy-feedback text-green-600 font-semibold ml-2 hidden">Copied!</span>
                                    <span class="text-gray-600">- Manual check-in link</span>
                                </li>
                                <li>
                                    <code class="copyable-placeholder cursor-pointer bg-gray-200 text-gray-800 rounded px-1 py-0.5">[link_feedback]</code>
                                    <span class="copy-feedback text-green-600 font-semibold ml-2 hidden">Copied!</span>
                                    <span class="text-gray-600">- Feedback form link</span>
                                </li>
                            </ul>
                        </div>

                        {{-- BARU: Notifikasi error jika tidak ada yg dipilih --}}
                        @error('selectedRegistrants') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        <div>
                            <button type="button" wire:click="sendBroadcast" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                {{-- DIUBAH: Teks Tombol --}}
                                Send to Selected Registrants
                            </button>

                            <button type="button" wire:click="saveTemplate" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 ml-2">
                                Save as Template
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Manage Check-ins</h3>
                    <div>
                        <label for="checkinDate" class="block text-sm font-medium text-gray-700">
                            Select date to view/edit attendance:
                        </label>
                        <input type="date"
                            id="checkinDate"
                            wire:model.live="selectedDate"
                            class="mt-1 block rounded-md border-gray-300 shadow-sm"
                            min="{{ Carbon\Carbon::parse($event->start_date)->toDateString() }}"
                            max="{{ Carbon\Carbon::parse($event->end_date)->toDateString() }}">
                    </div>
                </div>
            </div>

            {{-- Tabel Daftar Pendaftar --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Registrant List</h3>
                    <div class="mt-4 flex gap-4 mb-4 flex-col md:flex-row md:items-center">
                        {{-- Search Input --}}
                        <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search registrants..." class="w-full md:w-1/3" />

                        {{-- Filter Dropdown --}}
                        <select wire:model.live="filterType" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="all">Semua Peserta</option>
                            <option value="regular">Pendaftar Umum (Regular)</option>
                            <option value="invited">Tamu Undangan (Invited)</option>
                        </select>
                    </div>

                    {{-- Di suatu tempat di bagian atas, dekat tombol 'Export' --}}
                    <div class="mb-4 flex space-x-2">
                        {{-- Tombol Ekspor Pendaftar (yang sudah ada) --}}
                        <button wire:click="openExportModal" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Export Registrants...
                        </button>

                        {{-- TOMBOL BARU KITA --}}
                        <button wire:click="exportCheckinHistory" wire:loading.attr="disabled"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <span wire:loading.remove wire:target="exportCheckinHistory">
                                Export Check-in History
                            </span>
                            <span wire:loading wire:target="exportCheckinHistory">
                                Exporting...
                            </span>
                        </button>
                    </div>

                    <div class="overflow-x-auto shadow-md sm:rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    {{-- BARU: Kolom header untuk checkbox "Select All" --}}
                                    <th class="px-6 py-3">
                                        <input type="checkbox" wire:model="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RFID At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Broadcast History</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($registrants as $registration)
                                <tr>
                                    {{-- BARU: Kolom body untuk checkbox per pendaftar --}}
                                    <td class="px-6 py-4">
                                        <input type="checkbox" wire:model="selectedRegistrants" value="{{ $registration->id }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal">
                                        <div class="flex flex-col">
                                            {{-- Baris 1: Nama dan Badge --}}
                                            <div class="flex items-start gap-2 flex-wrap">
                                                {{-- Nama: Bisa diklik untuk modal detail --}}
                                                <div wire:click="showDetails({{ $registration->id }})"
                                                    class="font-bold text-sm text-blue-600 hover:text-blue-800 cursor-pointer transition-colors hover:underline">
                                                    {{ $registration->name }}
                                                </div>

                                                {{-- Badge: Tamu Undangan (Invitation System) --}}
                                                @if(($registration->data['source'] ?? '') === 'Invitation System')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-800 border border-purple-200" title="Peserta dari Jalur Undangan">
                                                    Invitation
                                                </span>
                                                @endif

                                                {{-- Badge: User Terdaftar (Linked Account) --}}
                                                @if($registration->user_id)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800 border border-blue-200" title="Memiliki Akun User Aplikasi">
                                                    User
                                                </span>
                                                @endif
                                            </div>

                                            {{-- ▼▼▼ TAMPILAN KHUSUS PERWAKILAN ▼▼▼ --}}
                                            @if(isset($registration->data['representing']))
                                            <div class="text-xs text-indigo-600 italic mt-0.5">
                                                ↳ Mewakili: <strong>{{ $registration->data['representing'] }}</strong>
                                            </div>
                                            @endif
                                            {{-- ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲ --}}

                                            {{-- Baris 2: Detail Kontak --}}
                                            <div class="mt-1 space-y-0.5">
                                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $registration->email }}
                                                </div>
                                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                    </svg>
                                                    {{ $registration->phone_number ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $registration->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{--
                                          1. @if mengecek apakah nilainya $registration->rfid_registered_at 
                                             itu ada (tidak null).
                                          2. Jika ada, kita gunakan \Carbon\Carbon::parse() untuk mengubahnya 
                                             menjadi objek tanggal (jika dia string) lalu memformatnya.
                                          3. @else akan menampilkan strip '-' jika datanya null/kosong.
                                        --}}
                                        @if($registration->rfid_registered_at)
                                        {{ \Carbon\Carbon::parse($registration->rfid_registered_at)->format('d-m-Y') }}
                                        @else
                                        -
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm align-top">
                                        {{-- Status untuk HARI INI --}}
                                        @if($registration->has_checked_in_today)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Checked-in Today</span>
                                        @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Not Today</span>
                                        @endif

                                        {{-- Riwayat Check-in --}}
                                        @if($registration->checkinLogs->isNotEmpty())
                                        <div class="mt-2 text-xs text-gray-500">
                                            <span class="font-semibold">History:</span>

                                            @php
                                            // 1. Ambil log, urutkan terbaru di atas
                                            $datesCheckedIn = $registration->checkinLogs
                                            ->sortByDesc('checkin_time')
                                            // 2. Ubah setiap log HANYA menjadi string tanggal
                                            ->map(function($log) {
                                            return \Carbon\Carbon::parse($log->checkin_time)->format('d M Y');
                                            })
                                            // 3. Ambil hanya tanggal yang unik
                                            ->unique()
                                            ->values();
                                            @endphp

                                            <ul class="list-disc list-inside">
                                                {{-- Loop pada setiap tanggal unik --}}
                                                @foreach($datesCheckedIn as $date)
                                                <li>{{ $date }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex flex-col space-y-1 items-start">
                                            @forelse($registration->broadcastHistories as $history) {{-- <-- UBAH DI SINI --}}
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" title="Sent on: {{ $history->created_at->format('d M Y, H:i') }}">
                                                {{ Str::limit($history->subject, 30) }}
                                            </span>
                                            @empty
                                            <span class="text-gray-400">-</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @php
                                        // Logika ini berfungsi karena 'checkinLogs'
                                        // sudah difilter oleh fungsi render() di backend.
                                        $isCheckedIn = $registration->checkinLogs->isNotEmpty();
                                        @endphp

                                        <button wire:click="toggleCheckIn({{ $registration->id }})"
                                            class="p-2 rounded-full {{ $isCheckedIn ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700' }}"
                                            title="{{ $isCheckedIn ? 'Click to Undo Check-in for '.$selectedDate : 'Click to Check-in for '.$selectedDate }}">

                                            @if($isCheckedIn)
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            @else
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                            </svg>
                                            @endif
                                        </button>



                                        <a href="{{ route('tickets.qrcode', $registration->uuid) }}" target="_blank" class="text-blue-600 hover:text-blue-900 ml-2">View QR</a>

                                        {{-- Logika Send Feedback Link perlu disesuaikan --}}
                                        @if($event->is_feedback_active)
                                        {{-- Hanya tampilkan jika sudah pernah check-in setidaknya sekali --}}
                                        @if($registration->checkinLogs->isNotEmpty())
                                        @if($registration->feedback_email_sent_at)
                                        <span class="ml-2 inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-gray-200 text-gray-700" title="Sent at: {{ $registration->feedback_email_sent_at->format('d M Y, H:i') }}">
                                            Feedback Sent
                                        </span>
                                        @else
                                        <button wire:click="sendFeedbackLink({{ $registration->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="sendFeedbackLink({{ $registration->id }})"
                                            class="ml-2 font-medium px-3 py-1 text-xs rounded-full bg-blue-200 text-blue-800 hover:bg-blue-300 disabled:opacity-50">
                                            Send Feedback
                                        </button>
                                        @endif
                                        @endif
                                        @endif

                                        <button
                                            @click.prevent="
                                                Swal.fire({
                                                    title: 'Are you sure?',
                                                    text: 'This registrant will be permanently deleted!',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#d33',
                                                    cancelButtonColor: '#3085d6',
                                                    confirmButtonText: 'Yes, delete it!'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $dispatch('delete-registration', { registrationId: {{ $registration->id }} })
                                                    }
                                                })
                                            "
                                            class="text-red-600 hover:text-red-900 ml-2">
                                            Delete
                                        </button>
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        No one has registered for this event yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $registrants->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($showDetailModal && $selectedRegistrantForDetail)
    <div class="z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            {{-- Latar belakang overlay --}}
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeDetailModal()"></div>

            {{-- Konten Modal --}}
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                        Detail Pendaftar
                    </h3>
                </div>

                {{-- Body Modal dengan Input Read-Only --}}
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[70vh] overflow-y-auto">
                    <div class="space-y-6">

                        {{-- Bagian 1: Informasi Registrasi --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" value="{{ $selectedRegistrantForDetail->name }}" readonly class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100 cursor-default">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="text" value="{{ $selectedRegistrantForDetail->email }}" readonly class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100 cursor-default">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
                                <input type="text" value="{{ $selectedRegistrantForDetail->phone_number }}" readonly class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100 cursor-default">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Daftar</label>
                                <input type="text" value="{{ $selectedRegistrantForDetail->created_at->format('d M Y, H:i') }}" readonly class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100 cursor-default">
                            </div>
                        </div>

                        {{-- Bagian 2: Data Tambahan dari Form Registrasi --}}
                        @if(is_array($selectedRegistrantForDetail->data) && !empty($selectedRegistrantForDetail->data))
                        <div class="border-t pt-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">Data Formulir Tambahan</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($selectedRegistrantForDetail->data as $key => $value)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $key)) }}</label>
                                    <input type="text" value="{{ is_array($value) ? implode(', ', $value) : $value }}" readonly class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100 cursor-default">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Bagian 3: Data Profil dari Tabel Users --}}
                        @if($selectedRegistrantForDetail->user && is_array($selectedRegistrantForDetail->user->profile_data) && !empty($selectedRegistrantForDetail->user->profile_data))
                        <div class="border-t pt-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">Data Profil User</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($selectedRegistrantForDetail->user->profile_data as $key => $value)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $key)) }}</label>
                                    <input type="text" value="{{ is_array($value) ? implode(', ', $value) : $value }}" readonly class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100 cursor-default">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="button" wire:click="closeDetailModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showExportModal)
    <div class="fixed z-20 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            {{-- Latar belakang overlay --}}
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeExportModal()"></div>

            {{-- Konten Modal --}}
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                        Pilih Kolom untuk di-Export
                    </h3>
                </div>

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <p class="text-sm text-gray-600 mb-4">Pilih data yang ingin Anda sertakan dalam file Excel.</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 max-h-60 overflow-y-auto border p-4 rounded-md">
                        @forelse($availableColumns as $key => $label)
                        <label class="flex items-center space-x-2 text-sm">
                            <input type="checkbox" wire:model.live="selectedColumns" value="{{ $key }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="text-gray-700">{{ $label }}</span>
                        </label>
                        @empty
                        <p class="col-span-full text-center text-gray-500">Tidak ada kolom tersedia.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="button"
                        @click="
                            Swal.fire({
                                title: 'Export Started!',
                                text: 'Your file will begin downloading shortly.',
                                icon: 'success',
                                timer: 3000,
                                showConfirmButton: false,
                            });
                            $wire.exportSelected();
                            $wire.closeExportModal();
                        "
                        wire:loading.attr="disabled"
                        wire:loading.class.remove="bg-green-600 hover:bg-green-700"
                        wire:loading.class="bg-green-400 cursor-not-allowed"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                        <svg wire:loading wire:target="exportSelected" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Download Excel
                    </button>
                    <button type="button" wire:click="closeExportModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        // ... (listener 'user-deleted' dan 'delete-failed' yang mungkin sudah ada sebelumnya)

        // TAMBAHKAN LISTENER BARU INI
        Livewire.on('registration-deleted', (event) => {
            Swal.fire({
                title: 'Deleted!',
                text: event.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });
        });

        // Anda bisa juga menambahkan listener 'delete-failed' jika belum ada
        Livewire.on('delete-failed', (event) => {
            Swal.fire({
                title: 'Oops!',
                text: event.message,
                icon: 'error',
            });
        });
    });
</script>
@endpush


{{-- PERUBAHAN 2: Menambahkan Javascript untuk fungsi copy-paste --}}
@script
<script>
    document.addEventListener('click', function(event) {
        // Cek apakah elemen yang diklik (atau parent-nya) memiliki kelas 'copyable-placeholder'
        const codeEl = event.target.closest('.copyable-placeholder');

        // Jika tidak, hentikan eksekusi. Ini bukan klik yang kita inginkan.
        if (!codeEl) {
            return;
        }

        // Ambil teks dari elemen <code>
        const textToCopy = codeEl.innerText;

        // Gunakan Clipboard API untuk menyalin teks
        navigator.clipboard.writeText(textToCopy).then(() => {
            const feedbackEl = codeEl.nextElementSibling;

            // Sembunyikan dulu SEMUA pesan "Copied!" lainnya agar tidak ada yang tumpang tindih
            document.querySelectorAll('.copy-feedback').forEach(el => {
                el.classList.add('hidden');
            });

            // Tampilkan pesan "Copied!" untuk elemen yang baru saja di-klik
            feedbackEl.classList.remove('hidden');

            // Sembunyikan lagi setelah 2 detik
            setTimeout(() => {
                feedbackEl.classList.add('hidden');
            }, 2000);
        }).catch(err => {
            // Tampilkan error di console jika penyalinan gagal (misal: di browser yang tidak aman)
            console.error('Failed to copy text: ', err);
        });
    });
</script>
@endscript

@script
<script>
    // Inisialisasi editor saat komponen dimuat
    const editor = ClassicEditor
        .create(document.querySelector('#broadcastContent'))
        .then(editor => {
            // Saat editor siap, set datanya dengan nilai awal dari Livewire
            editor.setData(@this.get('broadcastContent'));

            // Dengarkan perubahan di editor dan update properti Livewire
            editor.model.document.on('change:data', () => {
                @this.set('broadcastContent', editor.getData());
            });

            // Dengarkan event 'template-loaded' dari Livewire
            Livewire.on('template-loaded', (event) => {
                // Set konten editor dengan data dari template yang di-load
                editor.setData(event.detail.content);
            });
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endscript