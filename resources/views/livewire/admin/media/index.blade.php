<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Media Gallery & Albums</h2>
    </div>

    {{-- TAMPILAN DETAIL ALBUM (Jika ada album dipilih) --}}
    @if($selectedAlbum)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            
            {{-- Header Album --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b pb-4">
                <div>
                    <button wire:click="backToAlbums" class="text-sm text-gray-500 hover:text-gray-700 flex items-center mb-2 transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Daftar Album
                    </button>
                    <h3 class="text-xl font-bold text-gray-900">{{ $selectedAlbum->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Total: {{ count($selectedAlbum->all_photos) }} Foto</p>
                </div>

                <div class="flex gap-2">
                    {{-- Tombol Edit Nama --}}
                    <button wire:click="openEditModal({{ $selectedAlbum->id }})" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition text-sm font-medium">
                        Edit Nama
                    </button>

                    {{-- Tombol Upload Manual --}}
                    <button wire:click="$set('showUploadModal', true)" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-sm font-medium flex items-center shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Upload Komputer
                    </button>

                    {{-- Tombol Pilih Drive (BARU) --}}
                    <button wire:click="openDrivePicker" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm font-medium flex items-center shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                        Ambil dari Drive
                    </button>
                </div>
            </div>

            {{-- Pesan Flash --}}
            <x-action-message on="message" class="mb-4 bg-green-50 text-green-700 border border-green-200 p-3 rounded" />

            {{-- Grid Foto (HYBRID) --}}
            @if(count($selectedAlbum->all_photos) > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($selectedAlbum->all_photos as $photo)
                        <div class="group relative bg-gray-50 border rounded-lg overflow-hidden h-48 shadow-sm hover:shadow-md transition-all">
                            
                            {{-- Gambar (Thumbnail) --}}
                            {{-- Kita pakai 'thumb' dari Accessor Model yang sudah kita buat --}}
                            <img src="{{ $photo['thumb'] }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                            
                            {{-- Badge Sumber --}}
                            @if($photo['source'] == 'drive')
                                <span class="absolute top-2 right-2 bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow border border-white">
                                    DRIVE
                                </span>
                            @else
                                <span class="absolute top-2 right-2 bg-indigo-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow border border-white">
                                    LOCAL
                                </span>
                            @endif

                            {{-- Overlay Actions --}}
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                {{-- View --}}
                                <a href="{{ $photo['url'] }}" target="_blank" class="p-2 bg-white rounded-full text-gray-700 hover:text-blue-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>

                                {{-- Delete (Mengirim ID dan Type) --}}
                                <button wire:click="confirmDeletePhoto({{ $photo['id'] }}, '{{ $photo['source'] }}')" 
                                        class="p-2 bg-white rounded-full text-gray-700 hover:text-red-600 transition"
                                        title="Hapus Foto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada foto</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan mengupload foto atau ambil dari Google Drive.</p>
                </div>
            @endif
        </div>

    {{-- TAMPILAN DAFTAR ALBUM (Default) --}}
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-medium text-gray-900">Daftar Album</h3>
                <x-primary-button wire:click="$set('showAlbumModal', true)">
                    + Buat Album Baru
                </x-primary-button>
            </div>

            @if(session()->has('message'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('message') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($albums as $album)
                    <div class="group relative bg-white border rounded-xl shadow-sm hover:shadow-md transition-all overflow-hidden cursor-pointer"
                         wire:click="selectAlbum({{ $album->id }})">
                        
                        {{-- Cover Album (Ambil foto pertama dari local atau drive) --}}
                        <div class="h-48 bg-gray-100 overflow-hidden relative">
                            @php $cover = $album->all_photos->first(); @endphp
                            @if($cover)
                                <img src="{{ $cover['thumb'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            
                            {{-- Overlay Gradient --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            
                            <div class="absolute bottom-4 left-4 text-white">
                                <h4 class="font-bold text-lg truncate pr-4">{{ $album->name }}</h4>
                                <span class="text-xs bg-white/20 px-2 py-1 rounded backdrop-blur-sm">
                                    {{ $album->all_photos->count() }} Foto
                                </span>
                            </div>
                        </div>

                        {{-- Footer Card (Delete Action) --}}
                        <div class="p-3 bg-white flex justify-end border-t">
                            <button wire:click.stop="confirmDeleteAlbum({{ $album->id }})" 
                                    class="text-xs text-red-500 hover:text-red-700 font-medium px-2 py-1 hover:bg-red-50 rounded transition">
                                Hapus Album
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        Belum ada album. Silakan buat album baru.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- MODAL 1: Create Album --}}
    <x-dialog-modal wire:model="showAlbumModal">
        <x-slot name="title">Buat Album Baru</x-slot>
        <x-slot name="content">
            <div class="mt-4">
                <x-input-label for="newAlbumName" value="Nama Album" />
                <x-text-input id="newAlbumName" type="text" class="mt-1 block w-full" wire:model.defer="newAlbumName" />
                <x-input-error for="newAlbumName" class="mt-2" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showAlbumModal', false)" wire:loading.attr="disabled">Batal</x-secondary-button>
            <x-primary-button class="ml-2" wire:click="createAlbum" wire:loading.attr="disabled">Simpan</x-primary-button>
        </x-slot>
    </x-dialog-modal>

    {{-- MODAL 2: Edit Album --}}
    <x-dialog-modal wire:model="showEditModal">
        <x-slot name="title">Edit Album</x-slot>
        <x-slot name="content">
            <div class="mt-4">
                <x-input-label for="editingAlbumName" value="Nama Album" />
                <x-text-input id="editingAlbumName" type="text" class="mt-1 block w-full" wire:model.defer="editingAlbumName" />
                <x-input-error for="editingAlbumName" class="mt-2" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditModal', false)" wire:loading.attr="disabled">Batal</x-secondary-button>
            <x-primary-button class="ml-2" wire:click="updateAlbum" wire:loading.attr="disabled">Update</x-primary-button>
        </x-slot>
    </x-dialog-modal>

    {{-- MODAL 3: Upload Manual --}}
    <x-dialog-modal wire:model="showUploadModal">
        <x-slot name="title">Upload Foto (Manual)</x-slot>
        <x-slot name="content">
            <div class="mt-4">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center"
                     x-data="{ isDropping: false }"
                     @dragover.prevent="isDropping = true"
                     @dragleave.prevent="isDropping = false"
                     @drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                     :class="{ 'border-indigo-500 bg-indigo-50': isDropping }">
                    
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    <div class="mt-4 flex text-sm text-gray-600 justify-center">
                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                            <span>Upload a file</span>
                            <input id="file-upload" wire:model="files" type="file" class="sr-only" multiple x-ref="fileInput">
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                </div>
                <div wire:loading wire:target="files" class="mt-2 text-sm text-blue-600 font-medium animate-pulse">
                    Sedang memproses file...
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showUploadModal', false)" wire:loading.attr="disabled">Batal</x-secondary-button>
            <x-primary-button class="ml-2" wire:click="uploadFiles" wire:loading.attr="disabled">Upload</x-primary-button>
        </x-slot>
    </x-dialog-modal>

    {{-- MODAL 4: Google Drive Picker (BARU) --}}
    <x-modal name="gallery-file-picker" :show="$showFilePicker" maxWidth="7xl" focusable>
        <div class="p-4 h-[85vh] flex flex-col">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h2 class="text-lg font-semibold text-gray-800">Pilih Foto dari Google Drive</h2>
                <button wire:click="$set('showFilePicker', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            {{-- Panggil Komponen File Manager --}}
            @if($showFilePicker)
                @livewire('admin.file-manager.index', [
                    'isPicker' => true,
                    'eventNameToEmit' => 'fileSelected',
                    'filterType' => 'image' // Opsional: Hanya tampilkan gambar
                ], key('gallery-picker-'.time()))
            @endif
        </div>
    </x-modal>

</div>

@script
    <script>
        // Listener Konfirmasi Hapus (Universal untuk Album & Foto)
        $wire.on('show-delete-confirmation', (event) => {
            let context = event.context; // 'album' atau 'photo'
            
            // Pesan berbeda tergantung apa yang dihapus
            let textMsg = context === 'album' 
                ? "Album ini beserta seluruh fotonya akan dihapus permanen!" 
                : "Foto ini akan dihapus dari album!";

            Swal.fire({
                title: 'Anda yakin?',
                text: textMsg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Panggil fungsi eksekusi yang sesuai
                    if (context === 'album') {
                        $wire.dispatch('perform-delete-album');
                    } else {
                        $wire.dispatch('perform-delete-photo');
                    }
                }
            });
        });

        // Listener Notifikasi Sukses
        $wire.on('swal:success', (event) => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: event.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
    @endscript