<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">
                Advertisement Management
            </h1>
            <x-primary-button wire:click="create">Create New Ad</x-primary-button>
        </div>
        <div class="mt-6">
            <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search ads by headline..." class="w-full md:w-1/3" />
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
        @endif

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Image</th>
                        <th scope="col" class="px-6 py-3">Headline</th>
                        <th scope="col" class="px-6 py-3">Position</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ads as $ad)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">
                            @if($ad->hasMedia())
                            <img src="{{ $ad->getFirstMediaUrl('default', 'ad-tall') }}" class="w-16 h-auto object-contain">
                            @else
                            <span class="text-gray-400">No Image</span>
                            @endif
                        </td>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $ad->headline }}
                        </th>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">{{ $positions[$ad->position] ?? $ad->position }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if ($ad->is_active)
                            <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">Active</span>
                            @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <x-secondary-button wire:click="edit({{ $ad->id }})">Edit</x-secondary-button>
                            <x-danger-button wire:click="delete({{ $ad->id }})" wire:confirm="Are you sure you want to delete this ad?">Delete</x-danger-button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No advertisements found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $ads->links() }}
        </div>
    </div>

    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            {{ $isEditMode ? 'Edit Advertisement' : 'Create New Advertisement' }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-input-label for="headline" value="Headline" />
                    <x-text-input id="headline" type="text" class="mt-1 block w-full" wire:model.defer="headline" />
                    {{-- PERBAIKAN --}}
                    <x-input-error :messages="$errors->get('headline')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="url" value="URL (Link)" />
                    <x-text-input id="url" type="url" class="mt-1 block w-full" wire:model.defer="url" placeholder="https://example.com" />
                    {{-- PERBAIKAN --}}
                    <x-input-error :messages="$errors->get('url')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="position" value="Position" />
                    <select id="position" wire:model.defer="position" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select a position</option>
                        @foreach($positions as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    {{-- PERBAIKAN --}}
                    <x-input-error :messages="$errors->get('position')" class="mt-2" />
                    <p class="text-xs text-gray-500 mt-1">Responsive Left Ads (320x100), Responsive Right Ads (320x100), Responsive Top Ads (728x90)</p>
                </div>
                <div>
                    <x-input-label for="image" value="Image" />
                    <input id="image" type="file" class="mt-1 block w-full" wire:model="image">
                    {{-- PERBAIKAN --}}
                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    @if ($image)
                    <img src="{{ $image->temporaryUrl() }}" class="mt-4 w-32 h-auto">
                    @elseif ($existingImageUrl)
                    <img src="{{ $existingImageUrl }}" class="mt-4 w-32 h-auto">
                    @endif
                </div>
                <div class="flex items-center">
                    <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" wire:model.defer="is_active">
                    <x-input-label for="is_active" value=" Is Active?" class="ml-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal">Cancel</x-secondary-button>
            <x-primary-button class="ml-2" wire:click="save">Save Ad</x-primary-button>
        </x-slot>
    </x-dialog-modal>
</div>