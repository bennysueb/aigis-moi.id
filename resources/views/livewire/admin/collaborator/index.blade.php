<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Collaborators & Sponsors Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="md:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-700">Categories</h3>
                        <button wire:click="createCategory" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-500 transition">
                            + Add New
                        </button>
                    </div>

                    <div class="space-y-2" id="category-list">
                        @forelse($categories as $category)
                        <div data-id="{{ $category->id }}"
                            class="group flex items-center justify-between p-3 border rounded-lg cursor-pointer transition {{ $selectedCategory && $selectedCategory->id == $category->id ? 'bg-blue-50 border-blue-500 ring-1 ring-blue-500' : 'bg-gray-50 hover:bg-gray-100' }}"
                            wire:click="selectCategory({{ $category->id }})">

                            <div class="flex items-center gap-2">
                                <div class="cursor-move text-gray-400 hover:text-gray-600 handle-cat">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M7 2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold block text-sm">{{ $category->name }}</span>
                                    <span class="text-xs text-gray-500 uppercase">{{ $category->type }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1">
                                <button wire:click.stop="editCategory({{ $category->id }})" class="text-gray-400 hover:text-blue-600 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>

                                <button wire:click.stop="confirmDeleteCategory({{ $category->id }})" class="text-gray-400 hover:text-red-600 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-400 text-sm text-center py-4">No categories yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    @if($selectedCategory)
                    <div class="flex justify-between items-center mb-6 border-b pb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $selectedCategory->name }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500 uppercase">{{ $selectedCategory->type }}</span>
                        </div>
                        <button wire:click="createCollaborator" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
                            + Add Logo / Company
                        </button>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" id="collaborator-list">
                        @forelse($collaborators as $col)
                        <div data-id="{{ $col->id }}" class="group relative bg-white border border-gray-200 rounded-lg p-4 flex flex-col items-center justify-center hover:shadow-md transition handle-col">

                            <div class="h-24 w-full flex items-center justify-center mb-3 bg-gray-50 rounded">
                                @if($col->logo_url)
                                <img src="{{ $col->logo_url }}" alt="{{ $col->name }}" class="max-h-20 max-w-full object-contain">
                                @else
                                <span class="text-xs text-gray-400">No Logo</span>
                                @endif
                            </div>

                            <h4 class="font-medium text-sm text-center truncate w-full" title="{{ $col->name }}">{{ $col->name }}</h4>

                            @if($col->url_link)
                            <a href="{{ $col->url_link }}" target="_blank" class="text-xs text-blue-500 hover:underline truncate max-w-full mt-1">Visit Link &rarr;</a>
                            @endif

                            <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition bg-white/80 rounded p-1 shadow-sm">
                                <button wire:click="editCollaborator({{ $col->id }})" class="text-blue-600 hover:text-blue-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg></button>

                                <button wire:click="confirmDeleteCollaborator({{ $col->id }})" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg></button>
                            </div>

                            <div class="absolute top-2 left-2 text-gray-300 cursor-move">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-10 text-gray-400 border-2 border-dashed rounded-lg">
                            No collaborators in this category yet.
                        </div>
                        @endforelse
                    </div>
                    @else
                    <div class="h-full flex flex-col items-center justify-center text-gray-400 min-h-[300px]">
                        <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <p>Select or create a category on the left to manage logos.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-dialog-modal wire:model.live="showCategoryModal">
        <x-slot name="title">{{ $isEditing ? 'Edit Category' : 'Create Category' }}</x-slot>
        <x-slot name="content">
            <div class="grid gap-4">
                <div>
                    <x-input-label for="cat_name" value="Category Name" />
                    <x-text-input id="cat_name" type="text" class="mt-1 block w-full" wire:model="cat_name" placeholder="e.g. Media Partner, Platinum Sponsor" />
                    <x-input-error :messages="$errors->get('cat_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="cat_type" value="Display Type" />
                    <select id="cat_type" wire:model="cat_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="partner">Partner (Standard)</option>
                        <option value="sponsor">Sponsorship (Highlighted)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Sponsors usually displayed with bigger logos.</p>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showCategoryModal', false)" wire:loading.attr="disabled">Cancel</x-secondary-button>
            <x-primary-button class="ml-2" wire:click="saveCategory" wire:loading.attr="disabled">Save</x-primary-button>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showCollaboratorModal">
        <x-slot name="title">{{ $isEditing ? 'Edit Company' : 'Add Company' }}</x-slot>
        <x-slot name="content">
            <div class="grid gap-4">
                <div>
                    <x-input-label for="col_name" value="Company Name" />
                    <x-text-input id="col_name" type="text" class="mt-1 block w-full" wire:model="col_name" placeholder="Company Name (for Tooltip)" />
                    <x-input-error :messages="$errors->get('col_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label value="Logo Source" class="mb-2" />
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" class="form-radio text-indigo-600" name="logo_type" value="upload" wire:model.live="col_logo_type">
                            <span class="ml-2">Upload File</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" class="form-radio text-indigo-600" name="logo_type" value="url" wire:model.live="col_logo_type">
                            <span class="ml-2">External URL</span>
                        </label>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg border">
                    @if($col_logo_type === 'upload')
                    <x-input-label for="col_logo_file" value="Upload Image (Max 2MB)" />
                    <input type="file" id="col_logo_file" wire:model="col_logo_file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                    <x-input-error :messages="$errors->get('col_logo_file')" class="mt-2" />

                    @if ($col_logo_file)
                    <div class="mt-2">
                        <span class="text-xs text-gray-500">New Preview:</span>
                        {{-- AMAN: Cek dulu apakah file bisa dipreview --}}
                        @if (method_exists($col_logo_file, 'isPreviewable') && $col_logo_file->isPreviewable())
                        <img src="{{ $col_logo_file->temporaryUrl() }}" class="h-16 object-contain border rounded p-1 bg-white">
                        @else
                        <div class="h-16 flex items-center justify-center border rounded bg-gray-50 text-xs text-gray-400 p-2 text-center">
                            <span class="block">
                                Preview unavailable<br>
                                <span class="text-[10px] opacity-75">({{ $col_logo_file->getClientOriginalName() }})</span>
                            </span>
                        </div>
                        @endif
                    </div>
                    @endif
                    @else
                    <x-input-label for="col_logo_url_remote" value="Image URL" />
                    <x-text-input id="col_logo_url_remote" type="text" class="mt-1 block w-full" wire:model="col_logo_url_remote" placeholder="https://example.com/logo.png" />
                    <x-input-error :messages="$errors->get('col_logo_url_remote')" class="mt-2" />
                    @endif
                </div>

                <div>
                    <x-input-label for="col_url_link" value="Website Link (Optional)" />
                    <x-text-input id="col_url_link" type="text" class="mt-1 block w-full" wire:model="col_url_link" placeholder="https://company.com" />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showCollaboratorModal', false)" wire:loading.attr="disabled">Cancel</x-secondary-button>
            <x-primary-button class="ml-2" wire:click="saveCollaborator" wire:loading.attr="disabled">Save</x-primary-button>
        </x-slot>
    </x-dialog-modal>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {

            // ===============================================
            // 1. LOGIKA SWEETALERT (NOTIFIKASI & KONFIRMASI)
            // ===============================================
            Livewire.on('alert', (data) => {
                // Ambil payload data dari backend
                // data[0] berisi array ['type' => ..., 'message' => ..., 'options' => ...]
                let alertData = data[0];
                let options = alertData.options || {};

                Swal.fire({
                    icon: alertData.type,
                    title: alertData.message,
                    text: options.text || '',

                    // Konfigurasi Posisi & Timer (untuk Toast)
                    position: options.position || 'center',
                    timer: options.timer || null,
                    toast: options.toast || false,
                    showConfirmButton: options.showConfirmButton || (options.timer ? false : true),
                    timerProgressBar: options.timerProgressBar || false,

                    // Konfigurasi Tombol Konfirmasi (untuk Delete)
                    showCancelButton: options.showCancelButton || false,
                    confirmButtonText: options.confirmButtonText || 'OK',
                    cancelButtonText: options.cancelButtonText || 'Cancel',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then((result) => {
                    // Jika User Klik "Yes, Delete!"
                    if (result.isConfirmed && options.onConfirmed) {
                        // Panggil metode PHP di backend
                        @this.call(options.onConfirmed);
                    }
                });
            });


            // ===============================================
            // 2. LOGIKA SORTABLE (DRAG & DROP)
            // ===============================================

            // Sortable untuk Kategori
            let catEl = document.getElementById('category-list');
            if (catEl) {
                Sortable.create(catEl, {
                    handle: '.handle-cat',
                    animation: 150,
                    onEnd: function(evt) {
                        let order = [];
                        document.querySelectorAll('#category-list > div').forEach((el, index) => {
                            order.push({
                                value: el.getAttribute('data-id'),
                                order: index + 1
                            });
                        });
                        @this.call('updateCategoryOrder', order);
                    }
                });
            }

            // Sortable untuk Collaborator
            let initColSortable = () => {
                let colEl = document.getElementById('collaborator-list');
                if (colEl) {
                    Sortable.create(colEl, {
                        handle: '.handle-col',
                        animation: 150,
                        ghostClass: 'bg-indigo-100',
                        onEnd: function(evt) {
                            let order = [];
                            document.querySelectorAll('#collaborator-list > div').forEach((el, index) => {
                                order.push({
                                    value: el.getAttribute('data-id'),
                                    order: index + 1
                                });
                            });
                            @this.call('updateCollaboratorOrder', order);
                        }
                    });
                }
            };

            initColSortable();

            Livewire.hook('morph.updated', ({
                el,
                component
            }) => {
                initColSortable();
            });
        });
    </script>
</div>