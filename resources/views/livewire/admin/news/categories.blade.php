<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">
                {{ __('News Categories') }}
            </h1>
            <x-primary-button wire:click="create">Create New Category</x-primary-button>
        </div>
        <div class="mt-6">
            <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search categories by name..." class="w-full md:w-1/3" />
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('message') }}
        </div>
        @endif
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <div class="bg-white p-6 rounded-lg">
                <div class="max-w-7xl mx-auto">

                    <div class="space-y-4">
                        @forelse($categories as $category)
                        <div class="p-4 border rounded-md">
                            <div class="flex justify-between items-center">
                                <span class="font-bold">{{ $category->name }}</span>
                                <div>
                                    <button wire:click="edit({{ $category->id }})" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                                    <button wire:click="delete({{ $category->id }})" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-sm ml-2">Delete</button>
                                </div>
                            </div>

                            @if($category->children->isNotEmpty())
                            <div class="ml-6 mt-2 space-y-2 border-l pl-4">
                                @foreach($category->children as $child)
                                <div class="flex justify-between items-center">
                                    <span>— {{ $child->name }}</span>
                                    <div>
                                        <button wire:click="edit({{ $child->id }})" class="text-indigo-600 hover:text-indigo-900 text-xs">Edit</button>
                                        <button wire:click="delete({{ $child->id }})" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 text-xs ml-2">Delete</button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @empty
                        <p>No categories found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>


        @if($showModal)
        <div class="fixed z-10 inset-0 overflow-y-auto" wire:keydown.escape.window="closeModal()">
            <div class="flex items-center justify-center min-h-screen">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <form wire:submit.prevent="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                                {{ $isEditMode ? 'Edit Category' : 'Create Category' }}
                            </h3>
                            <div class="mt-4">
                                <label for="name_en" class="block text-sm font-medium text-gray-700">Name (EN)</label>
                                <input type="text" wire:model.defer="name_en" id="name_en" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('name_en') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mt-4">
                                <label for="name_id" class="block text-sm font-medium text-gray-700">Name (ID)</label>
                                <input type="text" wire:model.defer="name_id" id="name_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="mt-4">
                                <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Category</label>
                                <select wire:model.defer="parent_id" id="parent_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">-- None (Top Level Category) --</option>
                                    @foreach($allCategories as $category)
                                    {{-- Mencegah kategori menjadi induk dari dirinya sendiri saat edit --}}
                                    @if(!$this->category_id || $this->category_id != $category->id)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @error('parent_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Save</button>
                            <button type="button" wire:click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>