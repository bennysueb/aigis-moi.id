<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Invitation Manager: {{ $event->name }}</h2>
                {{-- Tambahkan wire:navigate agar loading lebih cepat --}}
                <a href="{{ route('admin.events.index') }}" class="text-gray-600 hover:text-gray-900" wire:navigate>&larr; Back to Events</a>
            </div>

            <x-action-message class="mb-4" on="notify" />

            @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">{{ session('message') }}</div>
            @endif
            @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">{{ session('error') }}</div>
            @endif

            {{-- Grid Utama --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- KOLOM KIRI: Tools (Import & Template) --}}
                <div class="space-y-6">

                    {{-- 1. Import Excel --}}
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4 border-b pb-2">1. Import Data Undangan</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">File Excel (.xlsx)</label>
                                <input type="file" wire:model="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <p class="text-xs text-gray-500">Pastikan header excel: <strong>name, email, phone, company, category</strong></p>
                            <x-primary-button wire:click="import" wire:loading.attr="disabled" class="w-full justify-center">
                                <span wire:loading.remove wire:target="import, file">Upload & Import</span>
                                <span wire:loading wire:target="import, file">Processing...</span>
                            </x-primary-button>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-medium text-gray-700">File Excel (.xlsx)</label>

                            {{-- Tombol Download Template --}}
                            <button type="button" wire:click="downloadTemplate" class="text-xs text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download Template
                            </button>
                        </div>
                    </div>

                    {{-- 2. Template Pesan & Surat --}}
                    <div class="bg-white p-6 rounded-lg shadow" x-data="{ tab: 'wa' }">
                        <h3 class="text-lg font-semibold mb-4 border-b pb-2">2. Pengaturan Pesan & Lampiran</h3>

                        {{-- Tab Switcher --}}
                        <div class="flex space-x-2 mb-4 border-b">
                            <button @click="tab = 'wa'" :class="tab === 'wa' ? 'border-b-2 border-green-500 text-green-600' : 'text-gray-500'" class="pb-2 px-2 text-sm font-medium">WhatsApp</button>
                            <button @click="tab = 'email'" :class="tab === 'email' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'" class="pb-2 px-2 text-sm font-medium">Email</button>
                            <button @click="tab = 'letter'" :class="tab === 'letter' ? 'border-b-2 border-purple-500 text-purple-600' : 'text-gray-500'" class="pb-2 px-2 text-sm font-medium">E-Letter & Files</button>
                        </div>

                        {{-- WA Config --}}
                        <div x-show="tab === 'wa'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Template WhatsApp</label>
                            <textarea wire:model="waTemplate" rows="6" class="w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>

                            {{-- Helper Variables dengan Fitur Copy (Versi WhatsApp) --}}
                            <div class="mt-3 bg-green-50 p-3 rounded-md border border-green-200">
                                <p class="text-xs font-bold text-green-800 mb-2">Variables (Klik untuk Copy):</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach([
                                    '{name}' => 'Nama Tamu',
                                    '{company}' => 'Instansi / Jabatan',
                                    '{event_name}' => 'Nama Event',
                                    '{link_surat}' => 'Link Lihat Surat (E-Letter)',
                                    '{link_konfirmasi}' => 'Link Konfirmasi Kehadiran'
                                    ] as $code => $desc)
                                    <div x-data="{ copied: false }"
                                        @click="navigator.clipboard.writeText('{{ $code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="flex items-center justify-between bg-white border border-green-100 rounded px-2 py-1.5 shadow-sm group cursor-pointer hover:border-green-400 transition-colors select-none">

                                        <div class="flex items-center min-w-0">
                                            <code class="text-green-700 font-mono font-bold text-xs bg-green-50 px-1 rounded border border-green-100">{{ $code }}</code>
                                            <span class="text-gray-500 text-[10px] ml-2 truncate">{{ $desc }}</span>
                                        </div>

                                        {{-- Ikon Berubah saat Dicopy --}}
                                        <div class="text-gray-400 group-hover:text-green-600">
                                            <span x-show="!copied"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                                </svg></span>
                                            <span x-show="copied" class="text-green-600 flex items-center text-[10px] font-bold" style="display: none;">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Copied!
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-primary-button type="button" wire:click="saveMessageSettings" wire:loading.attr="disabled" class="w-full justify-center bg-green-600 hover:bg-green-700 focus:ring-green-500">
                                    <span wire:loading.remove wire:target="saveMessageSettings">Simpan Template WhatsApp & Email</span>
                                    <span wire:loading wire:target="saveMessageSettings">Menyimpan...</span>
                                </x-primary-button>
                            </div>
                        </div>

                        {{-- Email Config --}}
                        <div x-show="tab === 'email'" class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email Subject</label>
                                <input type="text" wire:model="emailSubject" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email Body Intro</label>
                                <textarea wire:model="emailBody" rows="4" class="w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
                                <p class="text-xs text-gray-500 mt-1">Tombol konfirmasi & Link Surat akan otomatis ditambahkan di bawah teks ini.</p>

                                {{-- Helper Variables dengan Fitur Copy (Versi Email - Tema Biru) --}}
                                <div class="mt-3 bg-blue-50 p-3 rounded-md border border-blue-200">
                                    <p class="text-xs font-bold text-blue-800 mb-2">Variables (Klik untuk Copy):</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach([
                                        '{name}' => 'Nama Tamu',
                                        '{company}' => 'Instansi / Jabatan',
                                        '{event_name}' => 'Nama Event',
                                        '{link_surat}' => 'Link Lihat Surat (E-Letter)',
                                        '{link_konfirmasi}' => 'Link Konfirmasi Kehadiran'
                                        ] as $code => $desc)
                                        <div x-data="{ copied: false }"
                                            @click="navigator.clipboard.writeText('{{ $code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="flex items-center justify-between bg-white border border-blue-100 rounded px-2 py-1.5 shadow-sm group cursor-pointer hover:border-blue-400 transition-colors select-none">

                                            <div class="flex items-center min-w-0">
                                                <code class="text-blue-700 font-mono font-bold text-xs bg-blue-50 px-1 rounded border border-blue-100">{{ $code }}</code>
                                                <span class="text-gray-500 text-[10px] ml-2 truncate">{{ $desc }}</span>
                                            </div>

                                            {{-- Ikon Berubah saat Dicopy --}}
                                            <div class="text-gray-400 group-hover:text-blue-600">
                                                <span x-show="!copied">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                                    </svg>
                                                </span>
                                                <span x-show="copied" class="text-green-600 flex items-center text-[10px] font-bold" style="display: none;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Copied!
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                {{-- Akhir Helper Variables --}}
                                <div class="mt-4">
                                    <x-primary-button type="button" wire:click="saveMessageSettings" wire:loading.attr="disabled" class="w-full justify-center bg-blue-600 hover:bg-blue-700 focus:ring-blue-500">
                                        <span wire:loading.remove wire:target="saveMessageSettings">Simpan Template WhatsApp & Email</span>
                                        <span wire:loading wire:target="saveMessageSettings">Menyimpan...</span>
                                    </x-primary-button>
                                </div>
                            </div>
                        </div>

                        {{-- ▼▼▼ TAB BARU: E-LETTER & FILES ▼▼▼ --}}
                        <div x-show="tab === 'letter'" class="space-y-4">

                            {{-- 1. Upload Kop Surat --}}
                            <div class="border p-3 rounded-md bg-gray-50">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-sm font-bold text-gray-700">Kop Surat (Background)</label>

                                    {{-- Tombol Hapus Kop Surat --}}
                                    @if ($newLetterHeader || $existingLetterHeader)
                                    <button type="button"
                                        wire:click="confirmDeleteHeader"
                                        class="text-xs text-red-600 hover:text-red-800 flex items-center gap-1 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus Kop
                                    </button>
                                    @endif
                                </div>

                                {{-- Preview Gambar --}}
                                @if ($newLetterHeader)
                                <div class="relative mb-2">
                                    <img src="{{ $newLetterHeader->temporaryUrl() }}" class="h-24 w-auto object-contain border bg-white shadow-sm">
                                    <span class="absolute top-0 left-0 bg-green-500 text-white text-[10px] px-1 font-bold">New</span>
                                </div>
                                @elseif ($existingLetterHeader)
                                <div class="relative mb-2">
                                    <img src="{{ asset('storage/' . $existingLetterHeader) }}" class="h-24 w-auto object-contain border bg-white shadow-sm">
                                </div>
                                @endif

                                <input type="file" wire:model="newLetterHeader" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                <p class="text-[10px] text-gray-500 mt-1">Upload gambar (PNG/JPG) untuk header/kop surat resmi.</p>
                            </div>

                            {{-- 2. CKEditor Isi Surat --}}
                            <div wire:ignore>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Narasi Surat Undangan</label>

                                {{-- Textarea biasa yang akan diubah jadi CKEditor --}}
                                <textarea id="invitation_letter_body" wire:model.defer="letterBody" class="w-full h-32"></textarea>

                                {{-- Helper Variables --}}
                                <div class="mt-3 bg-purple-50 p-3 rounded-md border border-purple-200">
                                    <p class="text-xs font-bold text-purple-800 mb-2">Variables (Klik untuk Copy):</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach([
                                        '{name}' => 'Nama Tamu',
                                        '{company}' => 'Instansi / Jabatan',
                                        '{event_name}' => 'Nama Event',
                                        '{link_surat}' => 'Link Lihat Surat (E-Letter)',
                                        '{link_konfirmasi}' => 'Link Konfirmasi Kehadiran'
                                        ] as $code => $desc)
                                        <div x-data="{ copied: false }"
                                            @click="navigator.clipboard.writeText('{{ $code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="flex items-center justify-between bg-white border border-purple-100 rounded px-2 py-1.5 shadow-sm group cursor-pointer hover:border-purple-400 transition-colors select-none">
                                            <div class="flex items-center min-w-0">
                                                <code class="text-purple-700 font-mono font-bold text-xs bg-purple-50 px-1 rounded border border-purple-100">{{ $code }}</code>
                                                <span class="text-gray-500 text-[10px] ml-2 truncate">{{ $desc }}</span>
                                            </div>
                                            <div class="text-gray-400 group-hover:text-purple-600">
                                                <span x-show="!copied"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                                    </svg></span>
                                                <span x-show="copied" class="text-green-600 flex items-center text-[10px] font-bold" style="display: none;">Copied!</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- SCRIPT KHUSUS CKEDITOR (Letakkan tepat di bawah divnya) --}}
                            @push('scripts')
                            <script>
                                document.addEventListener('livewire:init', () => {
                                    ClassicEditor
                                        .create(document.querySelector('#invitation_letter_body'))
                                        .then(editor => {
                                            // 1. Set data awal
                                            editor.setData(@this.letterBody);

                                            // 2. Saat data berubah di editor, paksa update ke Livewire
                                            editor.model.document.on('change:data', () => {
                                                @this.set('letterBody', editor.getData());
                                            });
                                        })
                                        .catch(error => {
                                            console.error(error);
                                        });
                                });
                            </script>
                            @endpush

                            {{-- Akhir Script CKEditor --}}

                            {{-- 3. Upload Lampiran Tambahan --}}
                            <div class="border p-3 rounded-md bg-gray-50">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Lampiran Dokumen (Rundown, dll)</label>

                                {{-- List Lampiran Existing --}}
                                @if(!empty($existingAttachments))
                                <ul class="mb-3 space-y-1">
                                    @foreach($existingAttachments as $index => $path)
                                    <li class="text-xs flex justify-between items-center bg-white p-1 rounded border">
                                        <a href="{{ asset('storage/'.$path) }}" target="_blank" class="text-blue-600 hover:underline truncate max-w-[150px]">
                                            📄 {{ basename($path) }}
                                        </a>
                                        <button type="button" wire:click="removeAttachment({{ $index }})" class="text-red-500 font-bold px-2 hover:text-red-700">&times;</button>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif

                                <input type="file" wire:model="newAttachments" multiple class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                <p class="text-[10px] text-gray-500 mt-1">Bisa pilih banyak file sekaligus (PDF, Docx, dll).</p>
                            </div>

                            {{-- Tombol Simpan Khusus Tab Ini --}}
                            <div class="pt-2">
                                <x-primary-button type="button" wire:click="saveLetterSettings" wire:loading.attr="disabled" class="w-full justify-center bg-purple-600 hover:bg-purple-700 focus:ring-purple-500">
                                    <span wire:loading.remove wire:target="saveLetterSettings, newLetterHeader, newAttachments">Simpan Pengaturan Surat</span>
                                    <span wire:loading wire:target="saveLetterSettings, newLetterHeader, newAttachments">Menyimpan...</span>
                                </x-primary-button>
                            </div>
                        </div>
                        {{-- ▲▲▲ BATAS TAB BARU ▲▲▲ --}}

                    </div>
                </div>

                {{-- KOLOM KANAN: Daftar Undangan --}}
                <div class="lg:col-span-2 bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 border-b flex flex-col sm:flex-row justify-between items-center gap-4">
                        <h3 class="text-lg font-semibold">Daftar Undangan</h3>

                        {{-- Filters --}}
                        <div class="flex gap-2">
                            <select wire:model.live="filterStatus" class="border-gray-300 rounded-md text-sm">
                                <option value="all">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Hadir (Confirmed)</option>
                                <option value="represented">Diwakilkan</option>
                                <option value="declined">Tidak Hadir</option>
                            </select>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama..." class="border-gray-300 rounded-md text-sm">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontak</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Konfirmasi</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($invitations as $invite)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $invite->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $invite->company ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $invite->category }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-xs text-gray-600 flex items-center gap-1">
                                            <span class="w-4 text-center">📧</span> {{ $invite->email ?? '-' }}
                                            @if($invite->is_sent_email) <span class="text-green-500 text-xs" title="Email Sent">✔</span> @endif
                                        </div>
                                        <div class="text-xs text-gray-600 flex items-center gap-1">
                                            <span class="w-4 text-center">📱</span> {{ $invite->phone_number ?? '-' }}
                                            {{-- Tombol WA (Redirect) --}}
                                            @if($invite->phone_number)
                                            @php
                                            $linkSurat = route('invitation.letter', $invite->uuid);
                                            $linkKonfirmasi = route('invitation.confirm', $invite->uuid);

                                            // Replace semua variabel yang mungkin dipakai
                                            $msg = str_replace(
                                            ['{name}', '{company}', '{event_name}', '{link_surat}', '{link_konfirmasi}'],
                                            [$invite->name, $invite->company ?? '-', $event->name, $linkSurat, $linkKonfirmasi],
                                            $waTemplate
                                            );

                                            $phone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $invite->phone_number));
                                            $waUrl = "https://wa.me/" . $phone . "?text=" . urlencode($msg);
                                            @endphp
                                            <a href="{{ $waUrl }}" target="_blank"
                                                onclick="@this.markWaSent({{ $invite->id }})"
                                                class="p-1 rounded hover:bg-green-100 text-green-600" title="Kirim WhatsApp">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                                </svg>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($invite->status == 'pending')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                                        @elseif($invite->status == 'confirmed')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Hadir</span>
                                        @elseif($invite->status == 'represented')
                                        <div class="flex flex-col items-start">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mb-1">Diwakilkan</span>
                                            {{-- Tampilkan Nama Wakil --}}
                                            <div class="text-xs text-gray-600">
                                                Oleh: <span class="font-bold text-gray-800">{{ $invite->representative_data['name'] ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        @else
                                        <div class="flex flex-col items-start">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Menolak</span>
                                            {{-- Opsional: Tampilkan alasan penolakan jika ada --}}
                                            @if($invite->rejection_reason)
                                            <span class="text-[10px] text-gray-500 italic mt-1 max-w-[150px] truncate" title="{{ $invite->rejection_reason }}">
                                                "{{ $invite->rejection_reason }}"
                                            </span>
                                            @endif
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex justify-center items-center gap-2">

                                            {{-- 1. Tombol Email --}}
                                            @if($invite->email)
                                            <div class="relative group">
                                                <button wire:click="sendEmail({{ $invite->id }})"
                                                    class="p-1.5 rounded border transition-colors {{ $invite->is_sent_email ? 'bg-blue-100 text-blue-700 border-blue-300' : 'hover:bg-blue-50 text-blue-600 border-transparent' }}"
                                                    title="{{ $invite->is_sent_email ? 'Email Sudah Dikirim' : 'Kirim Email' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>

                                                    {{-- Badge Centang --}}
                                                    @if($invite->is_sent_email)
                                                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500 text-white text-[8px] items-center justify-center">✓</span>
                                                    </span>
                                                    @endif
                                                </button>
                                            </div>
                                            @endif

                                            {{-- 2. Tombol WhatsApp --}}
                                            @if($invite->phone_number)
                                            @php
                                            // 1. Generate Link Personal
                                            $linkSurat = route('invitation.letter', $invite->uuid);
                                            $linkKonfirmasi = route('invitation.confirm', $invite->uuid);

                                            // 2. Ambil Template (Priority: Inputan Livewire > Database Event > Default String)
                                            $template = $waTemplate ?: ($event->invitation_wa_template ?: 'Halo {name}, Link: {link_konfirmasi}');

                                            // 3. Lakukan Replacement
                                            $finalMsg = str_replace(
                                            ['{name}', '{company}', '{event_name}', '{link_surat}', '{link_konfirmasi}'],
                                            [
                                            $invite->name,
                                            $invite->company ?? '',
                                            $event->name,
                                            $linkSurat,
                                            $linkKonfirmasi
                                            ],
                                            $template
                                            );

                                            // 4. Format URL
                                            $phone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $invite->phone_number));
                                            $waUrl = "https://wa.me/" . $phone . "?text=" . rawurlencode($finalMsg);
                                            @endphp

                                            <div class="relative group">
                                                <a href="{{ $waUrl }}" target="_blank"
                                                    onclick="@this.markWaSent({{ $invite->id }})"
                                                    class="block p-1.5 rounded border transition-colors {{ $invite->is_sent_whatsapp ? 'bg-green-100 text-green-700 border-green-300' : 'hover:bg-green-50 text-green-600 border-transparent' }}"
                                                    title="{{ $invite->is_sent_whatsapp ? 'WA Sudah Dikirim' : 'Kirim WhatsApp' }}">

                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                                    </svg>

                                                    {{-- Badge Centang --}}
                                                    @if($invite->is_sent_whatsapp)
                                                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500 text-white text-[8px] items-center justify-center">✓</span>
                                                    </span>
                                                    @endif
                                                </a>
                                            </div>
                                            @endif

                                            {{-- Tombol Edit --}}
                                            <button wire:click="edit({{ $invite->id }})" class="p-1.5 rounded hover:bg-yellow-100 text-yellow-600 transition-colors" title="Edit Data">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            {{-- Tombol Hapus --}}
                                            <button type="button" wire:click="confirmDeleteInvitation({{ $invite->id }})" class="p-1.5 rounded hover:bg-red-100 text-red-600 transition-colors" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">Belum ada data undangan. Silakan import Excel.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t">
                        {{ $invitations->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if($isEditing)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

            {{-- Overlay Gelap (Klik untuk tutup) --}}
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" wire:click="cancelEdit"></div>

            {{-- Spacer agar modal di tengah --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Panel Modal --}}
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                {{-- Header & Form --}}
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                        Edit Data Undangan
                    </h3>

                    <div class="grid grid-cols-1 gap-4">
                        {{-- Nama --}}
                        <div>
                            <x-input-label for="edit_name" value="Nama Lengkap" />
                            <x-text-input id="edit_name" type="text" class="mt-1 block w-full" wire:model="editForm.name" />
                            <x-input-error :messages="$errors->get('editForm.name')" class="mt-2" />
                        </div>

                        {{-- Email & HP --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="edit_email" value="Email" />
                                <x-text-input id="edit_email" type="email" class="mt-1 block w-full" wire:model="editForm.email" />
                                <x-input-error :messages="$errors->get('editForm.email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="edit_phone" value="No. WhatsApp" />
                                <x-text-input id="edit_phone" type="text" class="mt-1 block w-full" wire:model="editForm.phone_number" />
                                <x-input-error :messages="$errors->get('editForm.phone_number')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Instansi & Kategori --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="edit_company" value="Instansi / Jabatan" />
                                <x-text-input id="edit_company" type="text" class="mt-1 block w-full" wire:model="editForm.company" />
                                <x-input-error :messages="$errors->get('editForm.company')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="edit_category" value="Kategori" />
                                <x-text-input id="edit_category" type="text" class="mt-1 block w-full" wire:model="editForm.category" />
                                <x-input-error :messages="$errors->get('editForm.category')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Tombol --}}
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="updateInvitation" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50" wire:loading.attr="disabled">
                        Simpan Perubahan
                    </button>
                    <button type="button" wire:click="cancelEdit" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL KONFIRMASI HAPUS UNDANGAN --}}
    @if($confirmingInvitationDeletion)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

            {{-- Overlay --}}
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" wire:click="cancelDeleteInvitation"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Hapus Data Undangan
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menghapus data undangan ini?
                                </p>

                                {{-- TAMPILKAN PERINGATAN JIKA SUDAH JADI PESERTA --}}
                                @if($registrationToDelete)
                                <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-3 text-left rounded-r">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-yellow-700">
                                                <span class="font-bold">Perhatian:</span>
                                                Tamu ini sudah terdaftar di menu <em>Registrants</em> ({{ $registrationToDelete->name }}).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    @if($registrationToDelete)
                    {{-- Opsi 1: Hapus Semua (Undangan + Registrasi) --}}
                    <button type="button" wire:click="deleteInvitation(true)" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:w-auto sm:text-sm" wire:loading.attr="disabled">
                        Hapus Undangan & Peserta
                    </button>

                    {{-- Opsi 2: Hapus Undangan Saja --}}
                    <button type="button" wire:click="deleteInvitation(false)" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none sm:w-auto sm:text-sm" wire:loading.attr="disabled">
                        Hapus Undangan Saja
                    </button>
                    @else
                    {{-- Jika belum registrasi, tombol hapus biasa --}}
                    <button type="button" wire:click="deleteInvitation" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" wire:loading.attr="disabled">
                        Ya, Hapus Data
                    </button>
                    @endif

                    <button type="button" wire:click="cancelDeleteInvitation" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL KONFIRMASI HAPUS KOP (Manual Implementation) --}}
    @if($confirmingHeaderDeletion)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

            {{-- Overlay Gelap --}}
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" wire:click="cancelDeleteHeader"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Panel Modal --}}
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            {{-- Icon Peringatan --}}
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Hapus Kop Surat
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menghapus Kop Surat ini? <br>
                                    Tindakan ini akan menghapus file gambar secara permanen dari server.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="deleteLetterHeader" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" wire:loading.attr="disabled">
                        Ya, Hapus Kop
                    </button>
                    <button type="button" wire:click="cancelDeleteHeader" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>