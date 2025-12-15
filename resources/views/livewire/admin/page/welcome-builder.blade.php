<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Welcome Page
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Homepage Sections
                        </h3>

                        {{-- TOMBOL BARU DITAMBAHKAN DI SINI --}}
                        <x-primary-button wire:click="openAddCustomSectionModal">
                            + Add Custom Section
                        </x-primary-button>

                    </div>


                    <p class="mt-1 text-sm text-gray-600">
                        Drag and drop to re-order. Use the toggle to show or hide a section.
                    </p>

                    <div class="mt-6 border-t border-gray-200">
                        <ul
                            x-data="{}"
                            x-init="Sortable.create($el, {
                                handle: '[wire\\:sortable\\.handle]',
                                onSort: (event) => {
                                    // Ambil urutan ID yang baru
                                    let items = Array.from(event.to.children).map(child => child.getAttribute('wire:key').replace('section-', ''));
                                    
                                    // Panggil metode Livewire untuk menyimpan ke database
                                    @this.call('updateOrder', items);

                                    // -- ANIMASI FLASH --
                                    // 1. Tambahkan kelas background untuk memulai animasi
                                    event.item.classList.add('bg-green-100');
                                    
                                    // 2. Hapus kelas tersebut setelah 1.5 detik
                                    setTimeout(() => {
                                        event.item.classList.remove('bg-green-100');
                                    }, 1500);
                                }
                            })"
                            class="divide-y divide-gray-200">

                            @forelse($sections as $section)
                            {{-- Kita gunakan wire:key sebagai sumber ID --}}
                            <li wire:key="section-{{ $section->id }}" class="py-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    {{-- Drag Handle --}}
                                    <svg wire:sortable.handle class="h-5 w-5 text-gray-400 cursor-grab mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                    </svg>

                                    {{-- ========================================= --}}
                                    {{-- PERUBAHAN 1: Paksa tampilkan 'en'      --}}
                                    {{-- ========================================= --}}
                                    <span class="text-sm font-medium {{ $section->is_visible ? 'text-gray-900' : 'text-gray-400 line-through' }}">
                                        {{ $section->getTranslation('name', 'en') }}
                                    </span>
                                </div>

                                <div class="flex items-center space-x-4">
                                    {{-- Tombol "Pilih Item" --}}
                                    @if(in_array($section->component, ['events', 'news']))
                                    <button wire:click="manageItems({{ $section->id }})" class="text-sm text-indigo-600 hover:text-indigo-900">
                                        Pilih Item
                                    </button>
                                    @elseif($section->component === 'banner')
                                    <a href="{{ route('admin.banners.index') }}" wire:navigate class="text-sm text-indigo-600 hover:text-indigo-900">
                                        Kelola Banner
                                    </a>
                                    @endif

                                    @if($section->custom_section_id)
                                    {{-- TOMBOL EDIT BARU --}}
                                    <button
                                        wire:click="editCustomSection({{ $section->id }})"
                                        class="text-gray-400 hover:text-indigo-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z"></path>
                                        </svg>
                                    </button>

                                    {{-- Tombol Delete yang sudah ada --}}
                                    <button
                                        wire:click="confirmDelete({{ $section->id }})"
                                        class="text-gray-400 hover:text-red-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    @endif

                                    {{-- Toggle Switch --}}
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:click="toggleVisibility({{ $section->id }})" class="sr-only peer" @if($section->is_visible) checked @endif>
                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>

                                </div>
                            </li>
                            @empty
                            <li>No sections found.</li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- ====================================================== --}}
                    {{-- == MODAL 1: PEMILIHAN SECTION TEMPLATE             == --}}
                    {{-- ====================================================== --}}
                    <x-dialog-modal wire:model.live="showTemplateSelectModal">
                        <x-slot name="title">
                            Select a Template
                        </x-slot>

                        <x-slot name="content">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @if($sectionTemplates)
                                @forelse($sectionTemplates as $template)
                                <div wire:click="selectTemplate({{ $template->id }})"
                                    class="border rounded-lg p-4 hover:bg-gray-100 hover:shadow-md cursor-pointer transition">

                                    {{-- ========================================= --}}
                                    {{-- PERUBAHAN 2: Paksa tampilkan 'en'      --}}
                                    {{-- ========================================= --}}
                                    <h4 class="font-semibold text-gray-800">
                                        {{ $template->getTranslation('name', 'en') }}
                                    </h4>
                                    <p class="text-sm text-gray-500 mt-1">Click to use this template</p>
                                </div>
                                @empty
                                <div class="col-span-full text-center text-gray-500">
                                    <p>No section templates found.</p>
                                    <a href="{{ route('admin.section-templates.index') }}" class="text-indigo-600 hover:underline">
                                        Create one now!
                                    </a>
                                </div>
                                @endforelse
                                @endif
                            </div>
                        </x-slot>

                        <x-slot name="footer">
                            <x-secondary-button wire:click="closeCustomSectionModals" wire:loading.attr="disabled">
                                Cancel
                            </x-secondary-button>
                        </x-slot>
                    </x-dialog-modal>

                    {{-- ====================================================== --}}
                    {{-- == MODAL 2: PENGISIAN KONTEN (SEKARANG MULTI-BAHASA) == --}}
                    {{-- ====================================================== --}}
                    <x-dialog-modal wire:model.live="showContentFillModal">
                        <x-slot name="title">
                            {{-- Judul Modal: Tetap pakai bahasa aktif admin --}}
                            Fill Content for: <span class="font-bold">{{ $selectedTemplate?->getTranslation('name', app()->getLocale()) }}</span>
                        </x-slot>

                        <x-slot name="content">
                            @if($selectedTemplate && isset($selectedTemplate->fields))

                            {{-- ========================================= --}}
                            {{-- BARU: Navigasi Tab Bahasa di dalam Modal --}}
                            {{-- ========================================= --}}
                            <div class="mb-6">
                                <div class="border-b border-gray-200">
                                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                        @foreach ($supportedLocales as $locale)
                                        <button
                                            type="button"
                                            {{-- Panggil setModalLocale saat tab diklik --}}
                                            wire:click.prevent="setModalLocale('{{ $locale }}')"
                                            @class([ 'whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm' , 'border-blue-600 text-blue-600'=> $modalLocale === $locale,
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => $modalLocale !== $locale,
                                            ])
                                            >
                                            {{ $locale === 'en' ? 'English' : 'Indonesian' }}
                                        </button>
                                        @endforeach
                                    </nav>
                                </div>
                            </div>

                            {{-- Form Fields --}}
                            <div class="space-y-4">
                                @foreach($selectedTemplate->fields as $field)
                                {{--
                                        BARU: Gunakan x-show untuk menampilkan/menyembunyikan field
                                        berdasarkan $modalLocale yang aktif.
                                        Kita loop SEMUA locale, tapi hanya tampilkan yang aktif.
                                    --}}
                                @foreach ($supportedLocales as $locale)
                                <div x-show="$wire.modalLocale === '{{ $locale }}'" style="display: none;">
                                    <label for="content-{{ $locale }}-{{ $field['name'] }}" class="block text-sm font-medium text-gray-700">
                                        {{ $field['label'] }} ({{ strtoupper($locale) }})
                                    </label>
                                    <div class="mt-1">
                                        @if($field['type'] === 'textarea')
                                        <textarea
                                            {{-- Binding ke struktur data nested: content.[locale].[field_name] --}}
                                            wire:model.defer="content.{{ $locale }}.{{ $field['name'] }}"
                                            id="content-{{ $locale }}-{{ $field['name'] }}"
                                            rows="4"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                        @else
                                        <input
                                            type="{{ $field['type'] === 'image' || $field['type'] === 'link' ? 'text' : $field['type'] }}" {{-- Tipe 'image' & 'link' jadi 'text' --}}
                                            {{-- Binding ke struktur data nested: content.[locale].[field_name] --}}
                                            wire:model.defer="content.{{ $locale }}.{{ $field['name'] }}"
                                            id="content-{{ $locale }}-{{ $field['name'] }}"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @endif
                                    </div>
                                    {{-- Menampilkan error spesifik per locale per field --}}
                                    @error("content.{$locale}.{$field['name']}") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                @endforeach
                                @endforeach
                            </div>
                            @endif
                        </x-slot>

                        <x-slot name="footer">
                            <x-secondary-button wire:click="closeCustomSectionModals" wire:loading.attr="disabled">
                                Cancel
                            </x-secondary-button>

                            <x-primary-button class="ml-3" wire:click="saveCustomSection" wire:loading.attr="disabled">
                                {{ $isEditMode ? 'Update Section' : 'Save Section' }} {{-- Ganti teks tombol saat edit --}}
                            </x-primary-button>
                        </x-slot>
                    </x-dialog-modal>
                    {{-- ====================================================== --}}
                    {{-- AKHIR DARI MODAL 2 --}}
                    {{-- ====================================================== --}}

                    {{-- ====================================================== --}}
                    {{-- == MODAL 3. KONFIRMASI HAPUS                           == --}}
                    {{-- ====================================================== --}}
                    <x-dialog-modal wire:model.live="showDeleteModal">
                        <x-slot name="title">
                            Delete Section
                        </x-slot>

                        <x-slot name="content">
                            Are you sure you want to delete this section? This action is permanent and cannot be undone.
                        </x-slot>

                        <x-slot name="footer">
                            <x-secondary-button wire:click="$set('showDeleteModal', false)" wire:loading.attr="disabled">
                                Cancel
                            </x-secondary-button>

                            <x-danger-button class="ml-3" wire:click="deleteCustomSection" wire:loading.attr="disabled">
                                Delete Section
                            </x-danger-button>
                        </x-slot>
                    </x-dialog-modal>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL "MANAGE ITEMS" TIDAK BERUBAH --}}
    @if($showItemModal && $managingSection)
    <div class="fixed z-50 inset-0 overflow-y-auto livewire-persistent-modal" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Kelola Konten untuk: <span class="font-bold">{{ $managingSection->getTranslation('name', app()->getLocale()) }}</span>
                    </h3>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800">Item Tersedia</h4>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari item..." class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm">

                            <ul class="mt-4 space-y-2 h-72 overflow-y-auto pr-2">
                                @forelse($this->availableItems as $item)
                                <li class="flex justify-between items-center p-2 rounded-md {{ collect($selectedItems)->pluck('id')->contains($item->id) ? 'bg-gray-100' : '' }}">
                                    <span class="text-sm text-gray-700">{{ $item->getTranslation('name', app()->getLocale()) ?? $item->getTranslation('title', app()->getLocale()) }}</span>
                                    @if(!collect($selectedItems)->pluck('id')->contains($item->id))
                                    <button wire:click="addItem({{ $item->id }})" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Tambah</button>
                                    @else
                                    <span class="text-gray-400 text-sm">Ditambahkan</span>
                                    @endif
                                </li>
                                @empty
                                <li class="text-sm text-gray-500">Tidak ada item ditemukan.</li>
                                @endforelse
                            </ul>
                            <div class="mt-4">
                                {{ $this->availableItems->links() }}
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800">Item Terpilih (Drag to reorder)</h4>

                            <ul x-data="{}" x-init="Sortable.create($el, {
                                handle: '.handle',
                                onSort: (event) => {
                                    let items = Array.from(event.to.children).map(child => child.getAttribute('data-id'));
                                    @this.call('updateSelectedOrder', items);
                                }
                            })" class="mt-4 space-y-2 h-72 overflow-y-auto pr-2">
                                @forelse($selectedItems as $item)
                                <li data-id="{{ $item['id'] }}" class="flex justify-between items-center p-2 bg-gray-50 rounded-md">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-gray-400 cursor-grab handle mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                        </svg>
                                        <span class="text-sm text-gray-800">{{ $item['title'] }}</span>
                                    </div>
                                    <button wire:click="removeItem({{ $item['id'] }})" class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </li>
                                @empty
                                <li class="text-sm text-gray-500 text-center py-4">Pilih item dari kolom kiri untuk ditambahkan.</li>
                                @endforelse
                            </ul>
                        </div>

                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm" wire:click="saveItems">
                        Simpan Perubahan
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" wire:click="closeModal">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Script SortableJS (tetap di sini) --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    {{-- ====================================================== --}}
    {{-- == TAMBAHKAN KODE SWEETALERT2 DI SINI               == --}}
    {{-- ====================================================== --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('swal:success', (event) => {
                const data = event[0];
                Swal.fire({
                    icon: 'success',
                    title: data.title,
                    text: data.text,
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        });
    </script>
    @endpush
</div>