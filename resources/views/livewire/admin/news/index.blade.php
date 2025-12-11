<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">
                {{ __('GreenNews Management') }}
            </h1>
            <x-primary-button wire:click="create">Create New Post</x-primary-button>
        </div>
        <div class="mt-6">
            <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search posts by title..." class="w-full md:w-1/3" />
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('message') }}
        </div>
        @endif

        <div class="overflow-hidden shadow-sm sm:rounded-lg"></div>
        <div class="p-6 bg-white border-b border-gray-200">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published At</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($posts as $post)
                        <tr wire:key="{{ $post->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{-- Gunakan thumbnail_url agar support Drive & Lokal --}}
                                <img src="{{ $post->thumbnail_url }}" alt="Thumbnail" class="w-16 h-12 object-cover rounded">
                            </td>
                            <td class="px-6 py-4 whitespace-normal">
                                <div class="text-sm font-medium text-gray-900">{{ $post->getTranslation('title', 'en') }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($post->getTranslation('title', 'id'), 40) }}</div>
                            </td>
                            <td class="px-6 py-4" style="min-width: 200px;">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($post->categories as $category)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{-- Tampilkan nama EN, atau ID jika EN tidak ada --}}
                                        {{ $category->getTranslation('name', 'en') ?: $category->getTranslation('name', 'id') }}
                                    </span>
                                    @empty
                                    <span class="text-xs text-gray-400">No Category</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $post->author->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $post->published_at ? $post->published_at->format('d M Y, H:i') : 'Draft' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="edit({{ $post->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button wire:click="delete({{ $post->id }})" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 ml-4">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                No posts found. Try creating one!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        </div>
    </div>





    {{-- Modal Form dengan Layout Sesuai Referensi --}}
    @if($showModal)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 opacity-75"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-6xl sm:w-full">
                <form wire:submit.prevent="save">
                    <div class="p-6 flex flex-col md:flex-row md:space-x-6">
                        {{-- Kolom Kiri (Konten Utama) --}}
                        <div class="w-full md:w-2/3 space-y-6">

                            {{-- Judul (EN & ID) --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="title_en" class="block text-sm font-medium text-gray-700">Title (EN)</label>
                                    <input type="text" wire:model.defer="title_en" id="title_en" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label for="title_id" class="block text-sm font-medium text-gray-700">Title (ID)</label>
                                    <input type="text" wire:model.defer="title_id" id="title_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            {{-- Konten (EN & ID) --}}
                            @if(in_array($type, ['article', 'video', 'audio', 'kebijakan']))
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Content</label>
                                <div class="mt-1 space-y-4">
                                    <div wire:ignore>
                                        <label for="content_en" class="block text-xs font-medium text-gray-500">English</label>
                                        <x-ckeditor wire:model.defer="content_en" id="content_en"></x-ckeditor>
                                    </div>
                                    <div wire:ignore>
                                        <label for="content_id" class="block text-xs font-medium text-gray-500">Indonesia</label>
                                        <x-ckeditor wire:model.defer="content_id" id="content_id"></x-ckeditor>
                                    </div>
                                </div>
                                {{-- Tampilkan error CKEditor secara manual jika perlu --}}
                                @error('content_en') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @error('content_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            {{-- Tanggal Publikasi --}}
                            <div>
                                <label for="published_at" class="block text-sm font-medium text-gray-700">Publish Date & Time</label>
                                <input type="datetime-local" wire:model.defer="published_at" id="published_at" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            {{-- Kotak untuk SEO --}}
                            <div class="p-4 border rounded-md">
                                <h4 class="font-semibold">SEO Details</h4>
                                <div class="mt-2 space-y-2">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Meta Title</label>
                                        <input type="text" wire:model.defer="seo_title" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Keywords (comma-separated)</label>
                                        <input type="text" wire:model.defer="seo_keywords" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Meta Description</label>
                                        <textarea wire:model.defer="seo_description" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan (Sidebar Metadata) --}}
                        <div class="w-full md:w-1/3 space-y-4 mt-6 md:mt-0">
                            {{-- Tipe Post --}}
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Post Type</label>
                                <select wire:model.live="type" id="type" class="mt-1 block w-full ...">
                                    <option value="article">Article</option>
                                    <option value="video">Video</option>
                                    <option value="press_release">Press Release</option>
                                    <option value="kebijakan">Kebijakan (Policy)</option>
                                    <option value="audio">Audio</option>
                                </select>
                            </div>

                            {{-- Form Dinamis berdasarkan Tipe --}}
                            @if($type === 'article')
                            <div class="p-4 border rounded-md space-y-2">
                                <h4 class="font-semibold text-sm">Article Source</h4>
                                <div><label class="text-xs">Source Name</label><input type="text" wire:model.defer="source_name" class="w-full text-sm ..."></div>
                                <div><label class="text-xs">Source URL</label><input type="url" wire:model.live.debounce.500ms="source_url" class="w-full text-sm ..."></div>
                            </div>
                            @elseif($type === 'video' || $type === 'audio')
                            <div class="p-4 border rounded-md">
                                <label class="block text-sm font-medium text-gray-700">{{ ucfirst($type) }} URL</label>
                                <input type="url" wire:model.defer="media_url" placeholder="e.g. YouTube, SoundCloud link" class="mt-1 block w-full ...">
                            </div>

                            @elseif($type === 'press_release' || $type === 'kebijakan')
                            <div class="p-4 border rounded-md space-y-2">
                                <h4 class="font-semibold text-sm">Document</h4>
                                <div>
                                    <label for="document_upload" class="text-xs">Upload File (PDF/Word, Max 20MB)</label>
                                    <input type="file" wire:model="document_upload" id="document_upload" class="w-full text-sm mt-1 block shadow-sm sm:text-sm border-gray-300 rounded-md">

                                    <div wire:loading wire:target="document_upload" class="text-xs text-blue-500 mt-1">Uploading...</div>

                                    @error('document_upload') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror

                                    {{-- Tampilkan file yang sudah ada --}}
                                    @if ($existing_document_url && !$document_upload)
                                    <div class="mt-2">
                                        <p class="text-xs font-medium text-gray-700">Existing Document:</p>
                                        <a href="{{ $existing_document_url }}" target="_blank" class="text-xs text-indigo-600 hover:underline">
                                            {{ basename(parse_url($existing_document_url, PHP_URL_PATH)) }}
                                        </a>
                                    </div>
                                    @endif

                                    {{-- Tampilkan preview file baru (hanya nama file) --}}
                                    @if ($document_upload)
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-500">New file: {{ $document_upload->getClientOriginalName() }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Gambar Unggulan --}}
                            <div class="p-4 border rounded-md bg-gray-50">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                                
                                {{-- Preview Gambar --}}
                                @if ($thumbnail)
                                    {{-- Preview Upload Baru Manual --}}
                                    <img src="{{ $thumbnail->temporaryUrl() }}" class="h-40 w-full object-cover rounded-md mb-3 shadow-sm bg-white border">
                                @elseif ($existingThumbnailUrl)
                                    {{-- Preview Existing (Lokal/Drive) --}}
                                    <div class="relative group h-40 w-full mb-3">
                                        <img src="{{ $existingThumbnailUrl }}" class="h-full w-full object-cover rounded-md shadow-sm bg-white border">
                                        
                                        {{-- Badge Indikator --}}
                                        @if($driveThumbnailPath) 
                                            <span class="absolute top-2 right-2 bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow border border-white">Drive</span>
                                        @else
                                            <span class="absolute top-2 right-2 bg-indigo-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow border border-white">Lokal</span>
                                        @endif
                                    </div>
                                @else
                                    {{-- Placeholder Kosong --}}
                                    <div class="h-40 w-full bg-white border-2 border-dashed border-gray-300 rounded-md mb-3 flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-xs">No Image Selected</span>
                                    </div>
                                @endif

                                {{-- Tombol Pilihan --}}
                                <div class="flex flex-col gap-2">
                                    {{-- Tombol Upload Manual --}}
                                    <label class="cursor-pointer bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-100 transition text-sm font-medium text-center shadow-sm flex items-center justify-center gap-2 w-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        Upload Komputer
                                        <input type="file" wire:model="thumbnail" class="hidden">
                                    </label>

                                    {{-- Tombol Pilih Drive --}}
                                    <button type="button" wire:click="openDrivePicker" class="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-md hover:bg-green-100 transition text-sm font-medium flex items-center justify-center gap-2 w-full shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                                        Pilih dari Drive
                                    </button>
                                </div>
                                <div wire:loading wire:target="thumbnail" class="text-xs text-blue-500 mt-2 text-center">Uploading...</div>
                                @error('thumbnail') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- BAGIAN KATEGORI BARU DENGAN TOM-SELECT --}}
                            <div class="p-4 border rounded-md">
                                <h4 class="font-semibold">Categories</h4>
                                <div class="mt-2 space-y-1 max-h-48 overflow-y-auto">
                                    {{-- MODIFIKASI: Loop bersarang untuk hierarki --}}
                                    @foreach($categories as $category)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="selectedCategories" value="{{ $category->id }}">
                                        <span class="ml-2 text-sm font-bold">{{ $category->name }}</span>
                                    </label>

                                    @if($category->children->isNotEmpty())
                                    <div class="ml-6 space-y-1">
                                        @foreach($category->children as $child)
                                        <label class="flex items-center">
                                            <input type="checkbox" wire:model="selectedCategories" value="{{ $child->id }}">
                                            <span class="ml-2 text-sm">{{ $child->name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                                @error('selectedCategories')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- AKHIR BAGIAN KATEGORI BARU --}}

                            {{-- Opsi Visibilitas --}}
                            <div class="p-4 border rounded-md">
                                <h4 class="font-semibold">Visibility & Flags</h4>
                                <div class="mt-2 space-y-2">
                                    <label class="flex items-center"><input type="checkbox" wire:model="visibility_options" value="featured"> <span class="ml-2 text-sm">Add to Featured</span></label>
                                    <label class="flex items-center"><input type="checkbox" wire:model="visibility_options" value="breaking"> <span class="ml-2 text-sm">Add to Breaking</span></label>
                                    <label class="flex items-center"><input type="checkbox" wire:model="visibility_options" value="recommended"> <span class="ml-2 text-sm">Add to Recommended</span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">

                        <x-primary-button type="submit" class="sm:ml-3" wire:loading.attr="disabled" wire:target="store">
                            <span wire:loading wire:target="store">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving Post...
                            </span>
                            <span wire:loading.remove wire:target="store">
                                Save Post
                            </span>
                        </x-primary-button>

                        <x-secondary-button type="button" wire:click="closeModal()" class="mt-3 sm:mt-0" wire:loading.attr="disabled" wire:target="store">
                            Cancel
                        </x-secondary-button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    
    {{-- MODAL PICKER (GOOGLE DRIVE) --}}
    <x-modal name="news-file-picker" :show="$showFilePicker" maxWidth="7xl" focusable>
        <div class="p-4 h-[85vh] flex flex-col">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h2 class="text-lg font-semibold text-gray-800">Pilih Thumbnail dari Drive</h2>
                <button wire:click="$set('showFilePicker', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            @if($showFilePicker)
                @livewire('admin.file-manager.index', [
                    'isPicker' => true,
                    'eventNameToEmit' => 'fileSelected',
                    'filterType' => 'image' // Filter khusus gambar
                ], key('news-picker-'.time()))
            @endif
        </div>
    </x-modal>
</div>