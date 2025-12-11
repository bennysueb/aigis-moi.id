<div>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Email Templates for: {{ $event->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session()->has('message'))
            <div class="mb-4 rounded-lg bg-green-100 p-4 text-sm text-green-700" role="alert">
                {{ session('message') }}
            </div>
            @endif
            @if (session()->has('error'))
            <div class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700" role="alert">
                {{ session('error') }}
            </div>
            @endif

            <div class="mb-4 flex justify-end">
                <x-primary-button wire:click="create">
                    Buat Template Baru
                </x-primary-button>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Daftar Template</h3>
                    <div class="mt-4 space-y-4">
                        @forelse ($templates as $template)
                        <div class="flex items-center justify-between p-4 border rounded-md">
                            <div class="flex items-center space-x-4">
                                {{-- BARU: Badge Global/Spesifik --}}
                                @if(is_null($template->event_id))
                                <span class="px-2 py-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded-full">Global</span>
                                @else
                                <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">Spesifik</span>
                                @endif
                                <div>
                                    <p class="font-bold">{{ $template->subject }}</p>
                                    <p class="text-sm text-gray-500">Dibuat pada: {{ $template->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <x-secondary-button wire:click="openSendModal({{ $template->id }})">Kirim</x-secondary-button>
                                <x-primary-button wire:click="edit({{ $template->id }})">Edit</x-primary-button>
                                <x-danger-button wire:click="delete({{ $template->id }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus template ini?">
                                    Hapus
                                </x-danger-button>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500">Belum ada template yang dibuat untuk event ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <div class="mt-8">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Riwayat Broadcast Acara Ini
                </h3>
                <div class="mt-4 bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        @forelse ($broadcastHistory as $broadcast)
                            <li>
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-indigo-600 truncate">
                                            {{ $broadcast->template->subject ?? 'Template Dihapus' }}
                                        </p>
                                        <div class="ml-2 flex-shrink-0 flex">
                                            <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @switch($broadcast->status)
                                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                                    @case('processing') bg-blue-100 text-blue-800 @break
                                                    @case('completed') bg-green-100 text-green-800 @break
                                                    @case('failed') bg-red-100 text-red-800 @break
                                                @endswitch">
                                                {{ ucfirst($broadcast->status) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="flex items-center text-sm text-gray-500">
                                                Dikirim pada: {{ $broadcast->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                            <p>Progress: {{ $broadcast->progress }} / {{ $broadcast->total_recipients }}</p>
                                        </div>
                                    </div>
                                    @if($broadcast->status == 'processing' || $broadcast->status == 'completed')
                                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2.5">
                                            @php
                                                $percentage = $broadcast->total_recipients > 0 ? ($broadcast->progress / $broadcast->total_recipients) * 100 : 0;
                                            @endphp
                                            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    @endif
                                    @if($broadcast->status == 'failed')
                                        <p class="mt-2 text-sm text-red-600">
                                            Error: {{ $broadcast->error_message }}
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li>
                                <div class="px-4 py-4 sm:px-6 text-center text-gray-500">
                                    Belum ada riwayat broadcast untuk acara ini.
                                </div>
                            </li>
                        @endforelse
                    </ul>
            
                    <div class="px-4 py-3">
                        {{ $broadcastHistory->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            {{ $editingTemplateId ? 'Edit Template Email' : 'Buat Template Email Baru' }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-input-label for="subject" value="Subjek Email" />
                    <x-text-input id="subject" type="text" class="block w-full mt-1" wire:model.blur="subject" />
                    @error('subject')
                    <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    @enderror
                </div>

                {{-- Checkbox untuk Template Global --}}
                <div class="flex items-center">
                    <input id="is_global" type="checkbox" wire:model.defer="is_global" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="is_global" class="ml-2 block text-sm text-gray-900">
                        Jadikan template ini global (bisa dipakai di semua event)
                    </label>
                </div>

                <div>
                    <x-input-label for="banner" value="Banner Email (Opsional)" />
                    <input id="banner" type="file" class="block w-full mt-1 text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" wire:model="banner">
                    @error('banner')
                    <x-input-error :messages="$errors->get('banner')" class="mt-2" />
                    @enderror

                    @if ($existingBannerPath)
                    <div class="mt-2">
                        <p class="text-sm text-gray-600">Banner saat ini:</p>
                        <img src="{{ asset('storage/' . $existingBannerPath) }}" class="object-cover w-48 mt-1 border rounded-md">
                    </div>
                    @endif
                </div>

                <div
                    wire:ignore
                    x-data="{ content: @entangle('content') }"
                    x-init="
                        ClassicEditor.create($refs.editor_content)
                            .then(editor => {
                                // Atur data awal jika ada (untuk mode edit)
                                if (content) {
                                    editor.setData(content);
                                }
                                
                                // Kirim perubahan kembali ke Livewire setiap kali ada ketikan
                                editor.model.document.on('change:data', () => {
                                    content = editor.getData();
                                });

                                // Dengar event 'set-content' dari Livewire (saat klik edit)
                                $wire.on('set-content', (event) => {
                                    editor.setData(event.content);
                                });

                                // ==========================================================
                                // == LISTENER YANG HILANG DITAMBAHKAN DI SINI ==
                                // ==========================================================
                                // Dengar event 'init-create-modal' dari Livewire (saat klik create)
                                $wire.on('init-create-modal', () => {
                                    editor.setData(''); // Reset editor menjadi kosong
                                });
                                // ==========================================================
                            })
                            .catch(error => console.error(error));
                    ">
                    <x-input-label for="content" value="Konten Email" />
                    <textarea x-ref="editor_content" id="content-editor-{{ rand() }}" class="hidden">{{ $content }}</textarea>
                </div>
                @error('content')
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
                @enderror

                <div class="p-4 mt-4 text-sm text-gray-700 bg-gray-100 border rounded-md">
                    <h4 class="font-bold">Placeholder yang Tersedia:</h4>
                    <ul class="mt-2 text-xs list-disc list-inside">
                        <li><code>@{{ nama_peserta }}</code> - Nama lengkap peserta.</li>
                        <li><code>@{{ nama_acara }}</code> - Nama acara.</li>
                        <li><code>@{{ tanggal_acara }}</code> - Placeholder tanggal acara (akan diganti saat email dikirim).</li>
                        <li><code>[info_acara_online]</code> - Menampilkan blok info online.</li>
                        <li><code>[info_lokasi_offline]</code> - Menampilkan blok info offline.</li>
                        <li><code>[gambar_qr_code]</code> - Menampilkan gambar QR Code tiket.</li>
                        <li><code>[tombol_lihat_tiket]</code> - Menampilkan tombol "Lihat Tiket Online".</li>
                    </ul>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal">
                Batal
            </x-secondary-button>

            <x-primary-button class="ml-2" wire:click="save">
                Simpan Template
            </x-primary-button>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showSendModal">
        <x-slot name="title">
            Kirim Email Broadcast
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <p>Anda akan mengirim email berdasarkan template ini. Pilih target pengiriman:</p>

                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" wire:model.live="sendTarget" value="test" class="form-radio text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2">Kirim email tes</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" wire:model.live="sendTarget" value="all" class="form-radio text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2">Kirim ke semua pendaftar ({{ $event->registrations->count() }} orang)</span>
                    </label>
                </div>

                @if ($sendTarget === 'test')
                <div class="pl-6">
                    <x-input-label for="testEmail" value="Alamat Email Tes" />
                    <x-text-input id="testEmail" type="email" class="block w-full mt-1" wire:model.blur="testEmail" placeholder="admin@example.com" />
                    @error('testEmail')
                    <x-input-error :messages="$errors->get('testEmail')" class="mt-2" />
                    @enderror
                </div>
                @endif

                @if ($sendTarget === 'all')
                <div class="p-4 mt-4 text-sm text-yellow-700 bg-yellow-100 border-l-4 border-yellow-500" role="alert">
                    <p class="font-bold">Perhatian!</p>
                    <p>Tindakan ini akan mengirim email ke semua peserta terdaftar. Pastikan konten email sudah benar sebelum melanjutkan.</p>
                </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showSendModal', false)">
                Batal
            </x-secondary-button>

            <x-primary-button class="ml-2" wire:click="sendEmail" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="sendEmail">Kirim Sekarang</span>
                <span wire:loading wire:target="sendEmail">Mengirim...</span>
            </x-primary-button>
        </x-slot>
    </x-dialog-modal>


</div>