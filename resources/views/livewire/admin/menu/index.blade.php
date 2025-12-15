<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menu Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                    @endif

                    <div class="flex justify-end mb-4">
                        <button wire:click="create()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Create New Menu Item
                        </button>
                    </div>

                    <div class="border rounded-lg p-4"
                        {{-- Inisialisasi Alpine.js untuk D&D --}}
                        x-data="{
        initSortable() {
            let root = this.$refs.menuRoot;

            Sortable.create(root, {
                group: 'menus',
                animation: 150,
                handle: '.handle',
                onEnd: (evt) => {
                    this.updateOrder();
                },
            });

            // Inisialisasi semua sub-list
            root.querySelectorAll('.submenu-list').forEach((list) => {
                Sortable.create(list, {
                    group: 'menus',
                    animation: 150,
                    handle: '.handle',
                    onEnd: (evt) => {
                        this.updateOrder();
                    },
                });
            });
        },
        updateOrder() {
            let items = [];
            // Ambil semua item root
            this.$refs.menuRoot.querySelectorAll(':scope > li[data-id]').forEach((li) => {
                items.push(this.serializeItem(li));
            });
            
            // Kirim data bersarang ke backend
            $wire.updateMenuOrder(items);
        },
        serializeItem(li) {
            let item = {
                value: li.dataset.id,
                items: []
            };
            // Cari submenu di dalam item ini
            let sublist = li.querySelector('.submenu-list');
            if (sublist) {
                sublist.querySelectorAll(':scope > li[data-id]').forEach((childLi) => {
                    item.items.push(this.serializeItem(childLi));
                });
            }
            return item;
        }
    }"
                        x-init="initSortable()">

                        @if($menuItems->isEmpty())
                        <p class="text-gray-500 text-center">No menu items created yet.</p>
                        @else

                        {{--
      Kita mengganti semua 'wire:sortable' dengan atribut 'data-id' 
      dan 'x-ref' untuk dibaca oleh JavaScript
    --}}
                        <ul class="space-y-2" x-ref="menuRoot">
                            @foreach($menuItems as $item)

                            <li data-id="{{ $item->id }}" wire:key="item-{{ $item->id }}" class="p-3 bg-gray-50 rounded-md shadow-sm">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        {{-- Handle untuk drag --}}
                                        <span class="handle cursor-move pr-2 text-gray-400 hover:text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                                <line x1="3" y1="18" x2="21" y2="18"></line>
                                            </svg>
                                        </span>
                                        <span class="font-semibold">{{ $item->label }}</span>
                                        <span class="text-sm text-gray-500 ml-2">({{ $item->link }})</span>
                                        @if($item->location)
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst(str_replace('_', ' ', $item->location)) }}
                                        </span>
                                        @endif
                                    </div>
                                    <div>
                                        <button wire:click="edit({{ $item->id }})" class="text-sm text-indigo-600 hover:text-indigo-800">Edit</button>
                                        <button wire:click="delete({{ $item->id }})" wire:confirm="Are you sure?" class="text-sm text-red-600 hover:text-red-800 ml-4">Delete</button>
                                    </div>
                                </div>

                                {{-- Ini adalah Child List (jika ada) --}}
                                {{-- Tambahkan class 'submenu-list' agar bisa di-query oleh JS --}}
                                <ul class="ml-8 space-y-2 mt-2 submenu-list">
                                    @foreach($item->children as $child)
                                    <li data-id="{{ $child->id }}" wire:key="item-{{ $child->id }}" class="p-3 bg-white rounded-md border">
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center">
                                                <span class="handle cursor-move pr-2 text-gray-400 hover:text-gray-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <line x1="3" y1="12" x2="21" y2="12"></line>
                                                        <line x1="3" y1="6" x2="21" y2="6"></line>
                                                        <line x1="3" y1="18" x2="21" y2="18"></line>
                                                    </svg>
                                                </span>
                                                <span class="font-semibold">{{ $child->label }}</span>
                                                <span class="text-sm text-gray-500 ml-2">({{ $child->link }})</span>
                                            </div>
                                            <div>
                                                <button wire:click="edit({{ $child->id }})" class="text-sm text-indigo-600 hover:text-indigo-800">Edit</button>
                                                <button wire:click="delete({{ $child->id }})" wire:confirm="Are you sure?" class="text-sm text-red-600 hover:text-red-800 ml-4">Delete</button>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($showModal)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit="save">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ $isEditMode ? 'Edit Menu Item' : 'Create New Menu Item' }}
                        </h3>
                        <div class="mt-6 space-y-4">
                            <div>
                                <label for="label_en" class="block text-sm font-medium text-gray-700">Label (EN)</label>
                                <input type="text" wire:model="label_en" id="label_en" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('label_en') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="label_id" class="block text-sm font-medium text-gray-700">Label (ID)</label>
                                <input type="text" wire:model="label_id" id="label_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('label_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="link" class="block text-sm font-medium text-gray-700">Link (URL)</label>
                                <input type="text" wire:model="link" id="link" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Menu (Optional)</label>
                                <select wire:model="parent_id" id="parent_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">-- No Parent --</option>
                                    @foreach($parentOptions as $id => $label)
                                    <option value="{{ $id }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="target" class="block text-sm font-medium text-gray-700">Target</label>
                                <select wire:model="target" id="target" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="_self">Open in same tab</option>
                                    <option value="_blank">Open in new tab</option>
                                </select>
                            </div>

                            {{-- =============================================== --}}
                            {{-- ==> BAGIAN BARU UNTUK TOGGLE & SELECT LOCATION <== --}}
                            {{-- =============================================== --}}
                            <div class="border-t pt-4">
                                <div class="flex items-center">
                                    <input id="showLocationSelector" wire:model.live="showLocationSelector" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="showLocationSelector" class="ml-2 block text-sm font-medium text-gray-900">
                                        Assign to a Specific Location
                                    </label>
                                </div>

                                @if($showLocationSelector)
                                <div class="mt-4">
                                    <label for="location" class="block text-sm font-medium text-gray-700">Menu Location</label>
                                    <select wire:model="location" id="location" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">-- Select Location --</option>
                                        <option value="header">Header</option>
                                        <option value="footer_nav">Footer (Navigation)</option>
                                        <option value="footer_legal">Footer (Legal)</option>
                                    </select>
                                    @error('location') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                @endif
                            </div>

                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 sm:ml-3 sm:w-auto sm:text-sm">Save</button>
                        <button type="button" wire:click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>