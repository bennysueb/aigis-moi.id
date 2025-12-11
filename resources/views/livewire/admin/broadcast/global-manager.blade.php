<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">
                Global Email Broadcast
            </h1>
            <x-primary-button wire:click="create">Create New Template</x-primary-button>
        </div>
        <div class="mt-6">
            <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search templates by subject..." class="w-full md:w-1/3" />
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        @if (session()->has('message'))
        <div class="mb-4 rounded-lg bg-green-100 p-4 text-sm text-green-700" role="alert">
            {{ session('message') }}
        </div>
        @endif

        <div>
            {{-- Bagian Manajemen Template --}}
            <div class="py-6">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                    {{-- Header Halaman (Responsive) --}}
                    <div class="mb-4 flex flex-col items-start space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                        <p class="text-gray-600">Total Penerima: <span class="font-bold">{{ $totalRecipients }}</span> pengguna unik.</p>
                        <x-primary-button wire:click="create" class="w-full sm:w-auto">
                            Buat Template Global Baru
                        </x-primary-button>
                    </div>

                    {{-- Daftar Template (Responsive) --}}
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-semibold">Daftar Template Global</h3>
                            <div class="mt-4 space-y-4">
                                @forelse ($templates as $template)
                                <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:items-left p-4 border rounded-md">
                                    {{-- Perubahan: sm:justify-between dihapus dari baris di atas --}}
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $template->subject }}</p>
                                        <p class="text-sm text-gray-500">Dibuat pada: {{ $template->created_at->format('d M Y, H:i') }}</p>
                                    </div>

                                    {{-- Grup Tombol (Responsive) --}}
                                    {{-- Perubahan: sm:ml-auto ditambahkan di baris di bawah --}}
                                    <div class="grid w-full grid-cols-2 gap-2 sm:w-auto sm:space-x-2 sm:ml-auto">
                                        <x-secondary-button class="justify-center" wire:click="openTestSendModal({{ $template->id }})">Uji Coba</x-secondary-button>
                                        <button wire:click="openSendModal({{ $template->id }})" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Kirim
                                        </button>
                                        <x-primary-button class="justify-center" wire:click="edit({{ $template->id }})">Edit</x-primary-button>
                                        <x-danger-button class="justify-center" wire:click="delete({{ $template->id }})" wire:confirm="Anda yakin ingin menghapus template global ini?">
                                            Hapus
                                        </x-danger-button>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500">Belum ada template global yang dibuat.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    @if($templates->hasPages())
                    <div class="mt-4">
                        {{ $templates->links() }}
                    </div>
                    @endif
                </div>

                {{-- Bagian Riwayat Broadcast --}}
                <div class="py-6">
                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-semibold">Riwayat Broadcast</h3>
                                <div class="mt-4 space-y-4">
                                    @forelse ($broadcastHistory as $item)
                                    <div class="p-4 border rounded-md">
                                        {{-- Header: Dibuat responsive --}}
                                        <div class="flex flex-col space-y-2 sm:space-y-0 sm:flex-row sm:items-left sm:justify-between">
                                            <div>
                                                <p class="font-bold text-gray-800">{{ $item->template->subject ?? 'Template Dihapus' }}</p>
                                                <p class="text-sm text-gray-500">Dibuat pada: {{ $item->created_at->format('d M Y, H:i') }}</p>
                                            </div>
                                            <div class="flex items-center self-start sm:self-center">
                                                <span @class([ 'px-2 py-1 text-xs font-semibold leading-tight rounded-full' , 'bg-yellow-100 text-yellow-800'=> in_array($item->status, ['pending', 'processing']),
                                                    'bg-green-100 text-green-800' => $item->status === 'sent',
                                                    'bg-red-100 text-red-800' => $item->status === 'failed',
                                                    ])>
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Progress Bar: Dibuat responsive --}}
                                        <div class="mt-4">
                                            <div class="flex flex-col sm:flex-row sm:justify-between mb-1 text-sm">
                                                <span class="font-medium text-gray-700">Progress</span>
                                                <span class="text-gray-500">{{ $item->processed_count }} / {{ $totalRecipients }} Terkirim</span>
                                            </div>
                                            @php
                                            // Menghindari division by zero
                                            $progress = $totalRecipients > 0 ? ($item->processed_count / $totalRecipients) * 100 : 0;
                                            @endphp
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-gray-500">Belum ada riwayat broadcast.</p>
                                    @endforelse
                                </div>

                                @if($broadcastHistory->hasPages())
                                <div class="mt-4">
                                    {{ $broadcastHistory->links('vendor.livewire.tailwind', ['pageName' => 'broadcastPage']) }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Modal Form Editor --}}
            <x-dialog-modal wire:model.live="showModal">
                <x-slot name="title">
                    {{ $editingTemplateId ? 'Edit Template Global' : 'Buat Template Global Baru' }}
                </x-slot>

                <x-slot name="content">
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="subject" value="Subjek Email" />
                            <x-text-input id="subject" type="text" class="block w-full mt-1" wire:model.blur="subject" />
                            @error('subject') <x-input-error :messages="$errors->get('subject')" class="mt-2" /> @enderror
                        </div>
                        <div>
                            <x-input-label for="banner" value="Banner Email (Opsional)" />
                            <input id="banner" type="file" class="block w-full mt-1" wire:model="banner">
                            @error('banner') <x-input-error :messages="$errors->get('banner')" class="mt-2" /> @enderror
                            @if ($existingBannerPath && !$banner)
                            <img src="{{ asset('storage/' . $existingBannerPath) }}" class="object-cover w-48 mt-2 border rounded-md">
                            @elseif ($banner)
                            <img src="{{ $banner->temporaryUrl() }}" class="object-cover w-48 mt-2 border rounded-md">
                            @endif
                        </div>
                        <div wire:ignore x-data="{ content: @entangle('content') }" x-init="
                    ClassicEditor.create($refs.editor_content)
                        .then(editor => {
                            if (content) { editor.setData(content); }
                            editor.model.document.on('change:data', () => { content = editor.getData(); });
                            $wire.on('set-content', (event) => { editor.setData(event.content); });
                            $wire.on('init-create-modal', () => { editor.setData(''); });
                        })
                        .catch(error => console.error(error));
                ">
                            <x-input-label for="content" value="Konten Email" />
                            <textarea x-ref="editor_content" class="hidden">{{ $content }}</textarea>
                        </div>
                        @error('content') <x-input-error :messages="$errors->get('content')" class="mt-2" /> @enderror
                        <div class="p-4 mt-4 text-sm text-gray-700 bg-gray-100 border rounded-md">
                            <h4 class="font-bold">Placeholder yang Tersedia:</h4>
                            <ul class="mt-2 text-xs list-disc list-inside">
                                <li><code>@{{ nama_peserta }}</code></li>
                                <li><code>@{{ app_name }}</code></li>
                            </ul>
                        </div>
                    </div>
                </x-slot>
                <x-slot name="footer">
                    <x-secondary-button wire:click="closeModal">Batal</x-secondary-button>
                    <x-primary-button class="ml-2" wire:click="save">Simpan Template</x-primary-button>
                </x-slot>
            </x-dialog-modal>

            {{-- Modal Pengiriman Email --}}
            <x-dialog-modal wire:model.live="showSendModal">
                <x-slot name="title">
                    Kirim Global Broadcast
                </x-slot>
                <x-slot name="content">
                    <div class="p-4 text-sm text-yellow-700 bg-yellow-100 border-l-4 border-yellow-500" role="alert">
                        <p class="font-bold">Perhatian!</p>
                        <p>Tindakan ini akan mengirim email ke **{{ $totalRecipients }}** pengguna unik. Pastikan konten dan subjek sudah benar.</p>
                    </div>
                </x-slot>
                <x-slot name="footer">
                    <x-secondary-button wire:click="$set('showSendModal', false)">Batal</x-secondary-button>

                    <x-danger-button class="ml-2" wire:click="confirmAndSendBroadcast" wire:loading.attr="disabled">
                        Kirim Sekarang
                    </x-danger-button>

                </x-slot>
            </x-dialog-modal>







            <x-dialog-modal wire:model.live="showTestSendModal">
                <x-slot name="title">
                    Kirim Email Uji Coba
                </x-slot>
                <x-slot name="content">
                    <x-input-label for="testEmail" value="Alamat Email Tes" />
                    <x-text-input id="testEmail" type="email" class="block w-full mt-1" wire:model.defer="testEmail" placeholder="admin@example.com" />
                    @error('testEmail')
                    <x-input-error :messages="$errors->get('testEmail')" class="mt-2" />
                    @enderror
                </x-slot>
                <x-slot name="footer">
                    <x-secondary-button wire:click="$set('showTestSendModal', false)">Batal</x-secondary-button>
                    <x-primary-button class="ml-2" wire:click="sendTestEmail" wire:loading.attr="disabled">
                        Kirim Tes
                    </x-primary-button>
                </x-slot>
            </x-dialog-modal>
        </div>
    </div>
</div>