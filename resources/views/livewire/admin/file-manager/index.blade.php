<div class="{{ $isPicker ? 'p-0 min-h-[400px]' : 'p-6 bg-white shadow-sm sm:rounded-lg min-h-screen' }} relative"
     x-data="{ isDropping: false, isUploading: false, progress: 0 }"
     x-on:livewire-upload-start="isUploading = true"
     x-on:livewire-upload-finish="isUploading = false"
     x-on:livewire-upload-error="isUploading = false"
     x-on:livewire-upload-progress="progress = $event.detail.progress"
     @dragover.prevent="isDropping = true"
     @dragleave.prevent="isDropping = false"
     @drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))">
    
    {{-- Upload Progress Bar --}}
    <div x-show="isUploading" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="fixed bottom-6 right-6 bg-white rounded-lg shadow-xl p-4 w-80 z-[9999] border border-indigo-100 flex flex-col gap-2"
         style="display: none;">
        
        {{-- Header Info --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg class="animate-bounce w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                <span class="text-sm font-semibold text-gray-700">Mengupload...</span>
            </div>
            <span class="text-xs font-bold text-indigo-600" x-text="progress + '%'"></span>
        </div>
        
        {{-- The Bar --}}
        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
            <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-200 ease-out shadow-[0_0_10px_rgba(79,70,229,0.5)]" 
                 :style="'width: ' + progress + '%'"></div>
        </div>

        <p class="text-[10px] text-gray-400 text-center">Jangan tutup halaman ini.</p>
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading.flex wire:target="navigate, navigateUp, refresh, deleteItem, createFolder, newUpload" 
         class="absolute inset-0 bg-white/80 z-50 flex items-center justify-center backdrop-blur-sm rounded-lg">
        <div class="flex flex-col items-center">
            <svg class="animate-spin h-10 w-10 text-indigo-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-600 font-medium">Processing...</span>
        </div>
    </div>

    {{-- Drag & Drop Overlay --}}
    <div x-show="isDropping" class="absolute inset-0 z-40 bg-indigo-50/90 border-4 border-dashed border-indigo-400 rounded-lg flex items-center justify-center transition-all" style="display: none;">
        <div class="text-center">
            <svg class="w-20 h-20 mx-auto text-indigo-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
            <h3 class="text-2xl font-bold text-indigo-700">Lepaskan file untuk Upload</h3>
        </div>
    </div>
    
    {{-- Disk Usage Indicator --}}
    @if(!$isPicker)
    <div class="mb-4 bg-white border border-gray-200 rounded-lg p-3 shadow-sm flex items-center justify-between">
        <div class="flex items-center space-x-3 w-full">
            {{-- Icon Drive --}}
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.01 1.485c2.082 0 3.754.02 4.959.084 1.268.067 2.29.298 3.16.853a5.55 5.55 0 0 1 2.228 2.228c.555.87.786 1.892.853 3.16.064 1.205.084 2.877.084 4.959 0 2.082-.02 3.754-.084 4.959-.067 1.268-.298 2.29-.853 3.16a5.55 5.55 0 0 1-2.228 2.228c-.87.555-1.892.786-3.16.853-1.205.064-2.877.084-4.959.084-2.082 0-3.754-.02-4.959-.084-1.268-.067-2.29-.298-3.16-.853a5.55 5.55 0 0 1-2.228-2.228c-.555-.87-.786-1.892-.853-3.16-.064-1.205-.084-2.877-.084-4.959 0-2.082.02-3.754.084-4.959.067-1.268.298-2.29.853-3.16a5.55 5.55 0 0 1 2.228-2.228c.87-.555 1.892-.786 3.16-.853 1.205-.064 2.877-.084 4.959-.084zm-7.39 9.39l2.76 4.79h5.27l-2.75-4.79h-5.28zm9.33-4.79l-2.76 4.79h5.28l2.76-4.79h-5.28zm-2.12 1.18l-5.27 9.14h5.28l5.27-9.14h-5.28z"/></svg>
            </div>
            
            <div class="flex-1">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-gray-700">Penyimpanan Google Drive</span>
                    <span class="text-gray-500">
                        {{ $diskQuota['used_formatted'] }} <span class="text-gray-300">/</span> {{ $diskQuota['total_formatted'] }}
                    </span>
                </div>
                
                {{-- Progress Bar --}}
                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full transition-all duration-1000 ease-out {{ $diskQuota['percent'] > 90 ? 'bg-red-500' : ($diskQuota['percent'] > 75 ? 'bg-yellow-400' : 'bg-green-500') }}" 
                         style="width: {{ $diskQuota['percent'] }}%"></div>
                </div>
            </div>

            {{-- Percentage Badge --}}
            <div class="text-xs font-bold px-2 py-1 bg-gray-100 rounded text-gray-600">
                {{ $diskQuota['percent'] }}%
            </div>
        </div>
    </div>
    @endif

    {{-- Toolbar --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 pb-4 border-b border-gray-100 gap-4">
        
        {{-- Left: Navigation / Search Status --}}
        {{-- HAPUS 'md:flex-1' agar tidak memakan tempat berlebih --}}
        <div class="flex items-center space-x-2 overflow-x-auto w-full md:w-auto max-w-full md:max-w-[40%]">
            @if(!empty($searchQuery))
                {{-- Tampilan saat Search --}}
                <div class="flex items-center text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-full text-sm font-medium whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span class="truncate max-w-[150px]">Hasil: "{{ $searchQuery }}"</span>
                    <button wire:click="$set('searchQuery', '')" class="ml-2 text-indigo-400 hover:text-indigo-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @else
                {{-- Tampilan Normal (Breadcrumbs) --}}
                <button wire:click="navigate('/')" class="p-2 rounded-full hover:bg-gray-100 text-gray-500 transition-colors flex-shrink-0" title="Home">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </button>
                
                @if($currentPath !== '/' && $currentPath !== '')
                    <button wire:click="navigateUp" class="p-2 rounded-full hover:bg-gray-100 text-gray-500 transition-colors flex-shrink-0" title="Go Up">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    </button>
                    <span class="text-gray-300 flex-shrink-0">|</span>
                @endif

                <nav class="flex text-gray-500 text-sm whitespace-nowrap overflow-x-auto no-scrollbar mask-linear-fade">
                    @foreach($breadcrumbs as $crumb)
                        <span class="mx-1">/</span>
                        <button wire:click="navigate('{{ $crumb['path'] }}')" class="hover:text-indigo-600 hover:underline truncate max-w-[100px]">
                            {{ $crumb['name'] }}
                        </button>
                    @endforeach
                </nav>
            @endif
        </div>

        {{-- Center: Search Input --}}
        {{-- UBAH lebar dari w-64 menjadi w-48 (lebih kecil) --}}
        <div class="w-full md:w-auto relative flex-shrink-0 md:mx-4">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" 
                   wire:model.live.debounce.500ms="searchQuery" 
                   placeholder="Cari file..." 
                   style="min-width: 250px;"
                   class="pl-10 w-full md:w-72 text-sm border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
        </div>

        {{-- Right: Actions & View Toggle --}}
        <div class="flex items-center space-x-3 w-full md:w-auto justify-end flex-shrink-0">
            
            {{-- VIEW TOGGLE --}}
            <div class="flex bg-gray-100 rounded-lg p-1 mr-2">
                <button wire:click="$set('viewMode', 'grid')" 
                        class="{{ $viewMode == 'grid' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700' }} p-1.5 rounded-md transition-all"
                        title="Grid View">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </button>
                <button wire:click="$set('viewMode', 'list')" 
                        class="{{ $viewMode == 'list' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700' }} p-1.5 rounded-md transition-all"
                        title="List View">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>

            {{-- Actions (Upload & New Folder) --}}
            @if(empty($searchQuery))
                <div class="relative flex items-center" x-data="{ open: @entangle('isCreatingFolder') }">
                    <template x-if="!open">
                        <button @click="open = true" class="flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm transition-colors whitespace-nowrap">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                            New Folder
                        </button>
                    </template>
                    <template x-if="open">
                        <div class="flex items-center space-x-2 animate-fade-in-right">
                            <input type="text" wire:model.live="newFolderName" placeholder="Name..." 
                                   class="w-24 md:w-32 px-2 py-1 text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                   @keydown.enter="$wire.createFolder()">
                            <button wire:click="createFolder" class="p-1 text-green-600 hover:bg-green-50 rounded"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></button>
                            <button @click="open = false" class="p-1 text-red-500 hover:bg-red-50 rounded"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>
                    </template>
                </div>

                <label for="gdrive-file-upload" class="flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm cursor-pointer transition-colors shadow-sm whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Upload
                </label>
                
                {{-- PERBAIKAN: Ubah 'id' menjadi ID unik --}}
                <input id="gdrive-file-upload" 
                       type="file" 
                       class="hidden" 
                       wire:model="newUpload" 
                       x-ref="fileInput"
                       accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,image/svg+xml">
            @endif
        </div>
    </div>
    
    {{-- Filter Tabs --}}
    <div class="flex space-x-1 mb-6 bg-gray-100 p-1 rounded-lg w-fit">
        <button wire:click="$set('filterType', 'all')" 
                class="px-4 py-1.5 text-sm font-medium rounded-md transition-all {{ $filterType === 'all' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Semua
        </button>
        <button wire:click="$set('filterType', 'image')" 
                class="px-4 py-1.5 text-sm font-medium rounded-md transition-all {{ $filterType === 'image' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Gambar
        </button>
        <button wire:click="$set('filterType', 'document')" 
                class="px-4 py-1.5 text-sm font-medium rounded-md transition-all {{ $filterType === 'document' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Dokumen
        </button>
    </div>

    {{-- Content Area --}}
    @if(empty($directories) && count($files) == 0)
        
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-20 text-gray-400 border-2 border-dashed border-gray-100 rounded-xl">
            <svg class="w-16 h-16 mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            <p class="text-lg">Folder ini kosong</p>
            <p class="text-sm">Upload file atau buat folder baru.</p>
        </div>

    @else
        
        {{-- TAMPILAN GRID --}}
        @if($viewMode === 'grid')
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                {{-- Loop Directories (Grid) --}}
                @foreach($directories as $dir)
                    <div class="group relative bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-200 rounded-xl p-4 transition-all duration-200 cursor-pointer flex flex-col items-center text-center"
                         wire:click="navigate('{{ $dir['path'] }}')">
                        <div class="mb-3 text-yellow-400 group-hover:scale-110 transition-transform">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 truncate w-full px-2">{{ $dir['name'] }}</span>
                        
                        <button wire:click.stop="deleteItem('{{ $dir['path'] }}', 'dir')" 
                                class="absolute top-2 right-2 p-1 bg-white rounded-full shadow-sm text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                @endforeach

                {{-- Loop Files (Grid) --}}
                @foreach($files as $file)
                    <div class="group relative bg-white hover:shadow-md border border-gray-200 rounded-xl p-4 transition-all duration-200 flex flex-col justify-between cursor-pointer"
                         {{-- LOGIKA KLIK UTAMA --}}
                         @if($isPicker)
                            wire:click="selectFile('{{ $file['path'] }}')"
                         @else
                            wire:click="triggerPreview('{{ $file['path'] }}', '{{ $file['mime_type'] }}')"
                         @endif
                    >
                        
                        {{-- Icon File --}}
                        <div class="flex-1 flex items-center justify-center mb-3 min-h-[60px]">
                            @if(str_starts_with($file['mime_type'], 'image/'))
                                <svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @elseif($file['mime_type'] === 'application/pdf')
                                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            @else
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            @endif
                        </div>

                        {{-- Nama & Ukuran File --}}
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-700 truncate w-full" title="{{ $file['name'] }}">{{ $file['name'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $file['size'] }}</p>
                        </div>

                        {{-- Hover Actions Overlay (HANYA SATU BLOK LOGIKA) --}}
                        @if($isPicker)
                            {{-- Tampilan Overlay Khusus Picker --}}
                            <div class="absolute inset-0 bg-indigo-500/10 border-2 border-indigo-500 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-full shadow-lg font-bold tracking-wide pointer-events-none">
                                    PILIH FILE
                                </span>
                            </div>
                        @else
                            {{-- Tampilan Overlay Normal (Download/Link/Delete) --}}
                            <div class="absolute inset-0 bg-black/50 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-2 backdrop-blur-[1px]">
                                <button wire:click.stop="downloadItem('{{ $file['path'] }}')" class="p-2 bg-white rounded-full hover:bg-gray-100 text-gray-700 transition-colors" title="Download">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </button>
                                
                                <button wire:click.stop="getShareLink('{{ $file['path'] }}')" class="p-2 bg-white rounded-full hover:bg-gray-100 text-blue-600 transition-colors" title="Copy Link">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </button>

                                <button wire:click.stop="deleteItem('{{ $file['path'] }}')" class="p-2 bg-white rounded-full hover:bg-red-50 text-red-600 transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

        {{-- TAMPILAN LIST (TABEL) --}}
        @else
            <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
               <table class="w-full min-w-[800px] divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                        <tr>
                            <th class="px-6 py-3 w-10"></th> {{-- Icon --}}
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3 w-32">Size</th>
                            <th class="px-6 py-3 w-40">Modified</th>
                            <th class="px-6 py-3 w-32 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        {{-- Loop Directories (List) --}}
                        @foreach($directories as $dir)
                            <tr class="hover:bg-gray-50 cursor-pointer group" wire:click="navigate('{{ $dir['path'] }}')">
                                <td class="px-6 py-3 text-center">
                                    <svg class="w-6 h-6 text-yellow-400 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                </td>
                                <td class="px-6 py-3 font-medium text-gray-700">
                                    {{ $dir['name'] }}
                                </td>
                                <td class="px-6 py-3 text-gray-400">-</td>
                                <td class="px-6 py-3 text-gray-400">
                                    {{ \Carbon\Carbon::createFromTimestamp($dir['last_modified'])->format('d M Y') }}
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <button wire:click.stop="deleteItem('{{ $dir['path'] }}', 'dir')" 
                                            class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                        {{-- Loop Files (List) --}}
                        @foreach($files as $file)
                            <tr class="hover:bg-gray-50 group"
                                {{-- Klik baris tabel --}}
                                @if($isPicker) wire:click="selectFile('{{ $file['path'] }}')" @endif 
                            >
                                <td class="px-6 py-3 text-center">
                                    {{-- Simple Icon Logic based on Mime --}}
                                    @if(str_starts_with($file['mime_type'], 'image/'))
                                        <svg class="w-5 h-5 text-purple-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @elseif($file['mime_type'] === 'application/pdf')
                                        <svg class="w-5 h-5 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <button 
                                        @if($isPicker)
                                            wire:click="selectFile('{{ $file['path'] }}')"
                                        @else
                                            wire:click="triggerPreview('{{ $file['path'] }}', '{{ $file['mime_type'] }}')"
                                        @endif
                                        class="text-gray-700 hover:text-indigo-600 hover:underline font-medium text-left">
                                        {{ $file['name'] }}
                                    </button>
                                </td>
                                <td class="px-6 py-3 text-gray-500">{{ $file['size'] }}</td>
                                <td class="px-6 py-3 text-gray-500 text-xs">
                                    {{ \Carbon\Carbon::createFromTimestamp($file['last_modified'])->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-3 text-right space-x-2">
                                    @if(!$isPicker)
                                    {{-- Actions --}}
                                    <button wire:click="downloadItem('{{ $file['path'] }}')" class="text-gray-400 hover:text-gray-600" title="Download">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    </button>
                                    
                                    <button wire:click="getShareLink('{{ $file['path'] }}')" class="text-gray-400 hover:text-blue-600" title="Copy Link">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    </button>

                                    <button wire:click="deleteItem('{{ $file['path'] }}')" class="text-gray-400 hover:text-red-600" title="Delete">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    @else
                                        {{-- Tombol Pilih di List View --}}
                                        <button wire:click="selectFile('{{ $file['path'] }}')" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs border border-indigo-200 bg-indigo-50 px-2 py-1 rounded">
                                            Pilih
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        
        @if($files->hasPages())
            <div class="mt-6">
                {{ $files->links() }} 
            </div>
        @endif
        
    @endif
    
    {{-- Script Global SweetAlert --}}
    @script
    <script>
        // Listener untuk Copy Link (yang sebelumnya)
        $wire.on('show-share-link', (event) => {
            // ... kode copy link yang lama biarkan saja ...
        });

        // --- TAMBAHAN BARU: Listener Delete Confirmation ---
        $wire.on('show-delete-confirmation', () => {
            Swal.fire({
                title: 'Anda yakin?',
                text: "File/Folder yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Panggil method destroy di Livewire
                    $wire.dispatch('perform-delete');
                }
            });
        });

        // Listener notifikasi sukses/error umum
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

        $wire.on('swal:error', (event) => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: event.message,
            });
        });
    </script>
    @endscript

    {{-- Script untuk Handle Copy Link --}}
    @script
    <script>
        $wire.on('show-share-link', (event) => {
            let url = event.link;
            navigator.clipboard.writeText(url).then(() => {
                // Tampilkan notifikasi (Optional, jika ada SweetAlert)
                if(typeof Swal !== 'undefined'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Link Copied!',
                        text: 'Link file berhasil disalin ke clipboard.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    alert('Link berhasil disalin: ' + url);
                }
            }).catch(err => {
                console.error('Gagal menyalin link', err);
            });
        });
    </script>
    
    {{-- Lightbox / Preview Modal --}}
    @if($previewFile)
        {{-- Ubah z-[60] menjadi z-[9999] agar pasti paling depan --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm p-4"
             x-data
             @keydown.escape.window="$wire.closePreview()">
            
            {{-- Tombol Close --}}
            <button wire:click="closePreview" class="absolute top-4 right-4 text-white hover:text-gray-300 p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            {{-- Container Gambar --}}
            <div class="relative max-w-5xl w-full max-h-screen flex flex-col items-center">
                
                {{-- Loading Indicator saat gambar dimuat --}}
                <div class="absolute inset-0 flex items-center justify-center text-white" wire:loading wire:target="triggerPreview">
                    <svg class="animate-spin h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                {{-- Gambar Preview --}}
                {{-- Kita gunakan route stream yang baru dibuat sebagai sumber gambar --}}
                @if(str_starts_with($previewFile['mime_type'], 'image/'))
                        {{-- Tampilan GAMBAR --}}
                        <img src="{{ route('admin.files.stream', ['path' => $previewFile['path']]) }}" 
                             alt="{{ $previewFile['name'] }}" 
                             class="max-w-full max-h-[80vh] object-contain mx-auto"
                             @click.stop>
                             
                    @elseif($previewFile['mime_type'] === 'application/pdf')
                        {{-- Tampilan PDF (Pakai Iframe) --}}
                        <iframe src="{{ route('admin.files.stream', ['path' => $previewFile['path']]) }}" 
                                class="w-full h-[80vh]" 
                                frameborder="0">
                        </iframe>
                    @endif
                
                {{-- Info File --}}
                <div class="mt-4 text-white text-center">
                    <p class="font-medium text-lg">{{ $previewFile['name'] }}</p>
                    <div class="flex items-center justify-center space-x-4 mt-2">
                        <button wire:click="downloadItem('{{ $previewFile['path'] }}')" class="text-sm bg-white/20 hover:bg-white/30 px-4 py-2 rounded-full transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Download
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @endscript
</div>