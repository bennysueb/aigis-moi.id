<div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1 bg-gray-50 p-4 rounded-lg border border-gray-200 h-fit">
            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $isEditing ? 'Edit Ticket' : 'Add New Ticket' }}</h3>

            <form wire:submit.prevent="save">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ticket Name</label>
                        <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" placeholder="e.g. VIP, Early Bird">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price (Rp)</label>
                        <input type="number" wire:model="price" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quota</label>
                            <input type="number" wire:model="quota" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Max/User</label>
                            <input type="number" wire:model="max_per_user" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea wire:model="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"></textarea>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm">
                        <span class="ml-2 text-sm text-gray-600">Active for sale</span>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        @if($isEditing)
                        <button type="button" wire:click="resetInput" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md text-sm">Cancel</button>
                        @endif
                        <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                            {{ $isEditing ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="md:col-span-2">
            @if (session()->has('message'))
            <div class="bg-green-100 text-green-700 p-2 rounded mb-4 text-sm">{{ session('message') }}</div>
            @endif

            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Name</th>
                            <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Price</th>
                            <th class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Quota</th>
                            <th class="px-3 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($tiers as $tier)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                {{ $tier->name }}
                                @if(!$tier->is_active) <span class="text-red-500 text-xs">(Inactive)</span> @endif
                                <div class="text-gray-500 text-xs font-normal">{{ Str::limit($tier->description, 30) }}</div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                Rp {{ number_format($tier->price, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {{ $tier->registrations_count ?? 0 }} / {{ $tier->quota }}
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium">
                                <button wire:click="edit({{ $tier->id }})" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                                <button wire:click="delete({{ $tier->id }})" onclick="return confirm('Are you sure?') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No tickets created yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>