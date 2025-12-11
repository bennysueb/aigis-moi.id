<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">
                {{ __('Manage Banners') }}
            </h1>
            <x-primary-button wire:click="create">Create New Banner</x-primary-button>
        </div>
        <div class="mt-6">
            <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search banners by headline..." class="w-full md:w-1/3" />
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('message') }}</p>
        </div>
        @endif

        <div class="max-w-7xl mx-auto"></div>
        <div class="flex flex-col">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">Banner Slides</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Drag and drop to re-order the slides. Only active banners will be shown on the homepage.
                    </p>

                    <div class="mt-6 border-t border-gray-200">
                        <ul x-data="{}" x-init="Sortable.create($el, {
                                handle: '[wire\\:sortable\\.handle]',
                                onSort: (event) => {
                                    let items = Array.from(event.to.children).map(child => child.getAttribute('wire:key').replace('banner-', ''));
                                    @this.call('updateOrder', items);
                                }
                            })"
                            class="divide-y divide-gray-200">

                            @forelse($banners as $banner)
                            <li wire:key="banner-{{ $banner->id }}" class="py-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg wire:sortable.handle class="h-5 w-5 text-gray-400 cursor-grab mr-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                    </svg>
                                    <img src="{{ $banner->getFirstMediaUrl('desktop_image') ?: 'https://via.placeholder.com/100x50' }}" alt="Desktop" class="h-12 w-24 object-cover rounded mr-4">
                                    <div>
                                        <p class="text-sm font-medium {{ $banner->is_active ? 'text-gray-900' : 'text-gray-400 line-through' }}">{{ $banner->headline }}</p>
                                        <span class="text-xs px-2 py-1 rounded-full {{ $banner->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $banner->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="space-x-2">
                                    <button wire:click="edit({{ $banner->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                    <button wire:click="delete({{ $banner->id }})" wire:confirm="Are you sure you want to delete this banner?" class="text-red-600 hover:text-red-900">Delete</button>
                                </div>
                            </li>
                            @empty
                            <li class="py-4 text-center text-gray-500">No banners found. Click "Add New Banner" to get started.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Form --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form wire:submit.prevent="save">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $isEditMode ? 'Edit Banner' : 'Create Banner' }}</h3>
                        <div class="mt-4 space-y-4">

                            <div>
                                <label for="headline" class="block text-sm font-medium text-gray-700">Headline</label>
                                <input type="text" wire:model="headline" id="headline" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('headline') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="subtitle" class="block text-sm font-medium text-gray-700">Subtitle</label>
                                <textarea wire:model="subtitle" id="subtitle" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>

                            <div>
                                <label for="features" class="block text-sm font-medium text-gray-700">Features (one per line)</label>
                                <textarea wire:model="features" id="features" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>

                            <div>
                                <label for="button_text" class="block text-sm font-medium text-gray-700">Button Text</label>
                                <input type="text" wire:model="button_text" id="button_text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label for="button_link" class="block text-sm font-medium text-gray-700">Button Link</label>
                                <input type="text" wire:model="button_link" id="button_link" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Desktop Image</label>
                                    <input type="file" wire:model="desktop_image" class="mt-1 block w-full text-sm">
                                    @error('desktop_image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    @if ($desktop_image) <img src="{{ $desktop_image->temporaryUrl() }}" class="mt-2 h-20"> @elseif($isEditMode) <img src="{{ $currentBanner->getFirstMediaUrl('desktop_image') }}" class="mt-2 h-20"> @endif
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Mobile Image</label>
                                    <input type="file" wire:model="mobile_image" class="mt-1 block w-full text-sm">
                                    @error('mobile_image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    @if ($mobile_image) <img src="{{ $mobile_image->temporaryUrl() }}" class="mt-2 h-20"> @elseif($isEditMode) <img src="{{ $currentBanner->getFirstMediaUrl('mobile_image') }}" class="mt-2 h-20"> @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-4">
                                <div>
                                    <label for="gradient_from" class="block text-sm font-medium text-gray-700">Gradient Color From</label>
                                    <div class="flex items-center mt-1">
                                        <input type="color" wire:model="gradient_from" id="gradient_from" class="h-10 w-10 p-1 border-gray-300 rounded-md shadow-sm">
                                        <input type="text" wire:model="gradient_from" class="ml-2 block w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                    @error('gradient_from') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="gradient_to" class="block text-sm font-medium text-gray-700">Gradient Color To</label>
                                    <div class="flex items-center mt-1">
                                        <input type="color" wire:model="gradient_to" id="gradient_to" class="h-10 w-10 p-1 border-gray-300 rounded-md shadow-sm">
                                        <input type="text" wire:model="gradient_to" class="ml-2 block w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                    @error('gradient_to') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="pt-4">
                                <label for="opacity" class="block text-sm font-medium text-gray-700">Gradient Opacity: <span x-text="$wire.opacity"></span></label>
                                <input type="range" wire:model="opacity" id="opacity" min="0" max="1" step="0.05" class="mt-1 block w-full">
                                @error('opacity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                    <span class="ml-2 text-sm text-gray-600">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Save
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    @endpush
</div>