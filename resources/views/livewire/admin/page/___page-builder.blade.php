<div>
    <form wire:submit.prevent="save" id="page-builder-form">
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Page Builder: <span class="font-normal">{{ $title['en'] ?? $this->page->title }}</span>
                </h2>
                <div>
                    <a href="{{ route('admin.pages.index') }}" class="text-sm text-gray-700 underline mr-4">Back to List</a>
                    <button type="submit" form="page-builder-form" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Save Page
                    </button>
                </div>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row lg:space-x-8">
                    {{-- SIDEBAR --}}
                    <aside class="w-full lg:w-1/4 mb-8 lg:mb-0">
                        <div class="bg-white p-6 rounded-lg shadow-sm sticky top-8">
                            <h3 class="font-bold text-lg mb-4">Available Sections</h3>
                            <div class="space-y-4">
                                @foreach($availableTemplates as $template)
                                <div wire:click="addBlock('{{ $template->slug }}')" class="p-4 border rounded-md hover:bg-gray-100 hover:shadow-md cursor-pointer transition">
                                    <h4 class="font-semibold">{{ $template->name }}</h4>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </aside>

                    {{-- MAIN CONTENT --}}
                    <main class="w-full lg:w-3/4">
                        <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
                            <h3 class="font-bold text-lg mb-4">Page Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="page_title_en" class="block text-sm font-medium text-gray-700">Title (EN)</label>
                                    <input type="text" wire:model.live="title.en" id="page_title_en" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('title.en') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="page_title_id" class="block text-sm font-medium text-gray-700">Title (ID)</label>
                                    <input type="text" wire:model.live="title.id" id="page_title_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('title.id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label for="page_slug" class="block text-sm font-medium text-gray-700">Slug</label>
                                    <input type="text" wire:model.live="slug" id="page_slug" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('slug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                                    @if ($errors->any())
                                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-2">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label for="page_status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select wire:model="status" id="page_status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- PAGE CONTENT BUILDER AREA --}}
                        <div class="bg-white p-6 rounded-lg shadow-sm min-h-screen">
                            <h3 class="font-bold text-lg mb-4">Page Content</h3>

                            {{-- Language Tabs --}}
                            <div class="mb-6">
                                <div class="border-b border-gray-200">
                                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                        @foreach ($supportedLocales as $locale)
                                        <button
                                            type="button"
                                            wire:click.prevent="setLocale('{{ $locale }}')"
                                            @class([ 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm' , 'border-blue-600 text-blue-600'=> $currentLocale === $locale,
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => $currentLocale !== $locale,
                                            ])
                                            >
                                            {{ $locale === 'en' ? 'English' : 'Indonesian' }}
                                        </button>
                                        @endforeach
                                    </nav>
                                </div>
                            </div>

                            {{-- 
                                MODIFIKASI FITUR DRAG & DROP
                                Kita membungkus list dengan x-data AlpineJS
                            --}}
                            <div 
                                x-data="{
                                    initSortable() {
                                        Sortable.create(this.$refs.blocksContainer, {
                                            animation: 150,
                                            handle: '.drag-handle', // Hanya bisa drag jika klik icon ini
                                            onEnd: () => {
                                                let orderedIds = Array.from(this.$refs.blocksContainer.children).map(el => el.dataset.id);
                                                $wire.updateBlockOrder(orderedIds);
                                            }
                                        });
                                    }
                                }"
                                x-init="initSortable"
                                class="space-y-6" 
                                x-ref="blocksContainer"
                            >
                                @forelse($pageBlocks[$currentLocale] as $index => $block)
                                {{-- 
                                    PENTING: tambahkan data-id agar JS tahu ID blok ini 
                                    wire:key HARUS ada agar Livewire tidak bingung saat urutan berubah
                                --}}
                                <div 
                                    data-id="{{ $block['id'] }}" 
                                    wire:key="{{ $currentLocale }}_{{ $block['id'] }}" 
                                    class="p-4 border-2 border-dashed rounded-md bg-gray-50 relative group"
                                >
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center gap-3">
                                            {{-- ICON DRAG HANDLE --}}
                                            <div class="drag-handle cursor-move text-gray-400 hover:text-gray-700 p-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <line x1="3" y1="12" x2="21" y2="12"></line>
                                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                                    <line x1="3" y1="18" x2="21" y2="18"></line>
                                                </svg>
                                            </div>
                                            
                                            <h4 class="font-semibold text-gray-800">
                                                Section: <span class="text-indigo-600">{{ $block['template_slug'] }}</span>
                                            </h4>
                                        </div>
                                        
                                        <div>
                                            <button type="button" wire:click="editBlock('{{ $block['id'] }}')" class="text-sm text-blue-600 hover:text-blue-800 font-semibold mr-4">
                                                Edit Content
                                            </button>
                                            <button type="button" wire:click="removeBlock('{{ $block['id'] }}')" class="text-sm text-red-600 hover:text-red-800" wire:confirm="Are you sure want to remove this section?">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                    
                                    {{-- Preview data ringkas (Opsional, agar user tahu isinya apa) --}}
                                    <div class="mt-2 text-xs text-gray-500 pl-9">
                                        @if(isset($block['data']['heading']) && $block['data']['heading'])
                                            Heading: {{ Str::limit($block['data']['heading'], 50) }}
                                        @elseif(isset($block['data']['content']))
                                             {{ Str::limit(strip_tags($block['data']['content']), 60) }}
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-12 border-2 border-dashed rounded-md bg-gray-50" data-id="empty-placeholder">
                                    <p class="text-gray-500">This page is empty for {{ $currentLocale === 'en' ? 'English' : 'Indonesian' }}.</p>
                                    <p class="text-gray-500 mt-2">Add a section from the library on the left to get started.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>

        {{-- Modal Edit (Tidak ada perubahan) --}}
        @if($showEditModal && $editingBlockTemplate)
        <div class="fixed z-50 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Editing: {{ $editingBlockTemplate->name }}</h3>
                        <div class="mt-6 space-y-4">
                            @foreach($editingBlockTemplate->fields as $field)
                            <div>
                                <label for="field-{{ $field['name'] }}" class="block text-sm font-medium text-gray-700">{{ $field['label'] }}</label>
                                @if($field['type'] === 'textarea')
                                <textarea wire:model="formData.{{ $field['name'] }}" id="field-{{ $field['name'] }}" rows="5" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                @else
                                <input type="text" wire:model="formData.{{ $field['name'] }}" id="field-{{ $field['name'] }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="updateBlock()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-white sm:ml-3 sm:w-auto sm:text-sm hover:bg-blue-700">Save Changes</button>
                        <button type="button" wire:click="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm hover:bg-gray-50">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </form>
</div>