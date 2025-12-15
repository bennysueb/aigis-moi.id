<div>
    <div class="p-6 bg-white shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Social Wall Management</h1>
            <div class="space-x-2">
                <button wire:click="openTypeModal" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">+ Add Type</button>
                <button wire:click="openItemModal" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">+ Add Item</button>
            </div>
        </div>

        <!-- Area Manajemen Tipe Sosial Media -->
        <div class="mb-8 p-4 border rounded-lg">
            <h2 class="text-lg font-semibold mb-3">Manage Social Media Types</h2>
            <div class="space-y-2">
                @forelse ($socialMediaTypes as $type)
                <div wire:key="type-{{ $type->id }}" class="flex justify-between items-center p-2 border-b">
                    <div class="flex items-center">
                        <i class="{{ $type->icon_class }} w-6 text-center"></i>
                        <span class="ml-3">{{ $type->name }}</span>
                    </div>
                    <div class="space-x-3">
                        <button wire:click="editType({{ $type->id }})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-pen-to-square"></i></button>
                        <button wire:click="confirmDeleteType({{ $type->id }})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-can"></i></button>
                    </div>
                </div>
                @empty
                <p class="text-gray-500">No social media types found. Add one to get started.</p>
                @endforelse
            </div>
        </div>

        <!-- Responsive Items List -->
        <div class="space-y-4">
            @forelse ($socialWallItems as $item)
            <div wire:key="item-{{ $item->id }}" class="bg-white rounded-lg shadow p-4 border">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                    <!-- Preview -->
                    <div class="md:col-span-2">
                        <span class="font-bold md:hidden">Preview:</span>
                        <div class="transform scale-50 origin-top-left w-[200%] h-48 overflow-hidden">{!! $item->embed_code !!}</div>
                    </div>
                    <!-- Type -->
                    <div class="">
                        <span class="font-bold md:hidden">Type:</span>
                        <span class="flex items-center">
                            <i class="{{ $item->socialMediaType->icon_class }} w-6 text-center"></i>
                            <span class="ml-2">{{ $item->socialMediaType->name }}</span>
                        </span>
                    </div>
                    <!-- Author & Status -->
                    <div class="">
                        <span class="font-bold md:hidden">Author:</span> {{ $item->user->name }}
                        <div class="mt-2 md:mt-0">
                            <span class="font-bold md:hidden">Status:</span>
                            <button wire:click="togglePublish({{ $item->id }})"
                                class="px-3 py-1 text-sm rounded-full {{ $item->is_published ? 'bg-green-500 text-white' : 'bg-yellow-500 text-white' }}">
                                {{ $item->is_published ? 'Published' : 'Draft' }}
                            </button>
                        </div>
                    </div>
                    <!-- Actions -->
                    <div class="">
                        <span class="font-bold md:hidden">Actions:</span>
                        <div class="flex items-center space-x-3">
                            <button wire:click="editItem({{ $item->id }})" class="text-blue-600 hover:text-blue-800"><i class="fas fa-pen-to-square"></i></button>
                            <button wire:click="confirmDeleteItem({{ $item->id }})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-can"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-gray-500">
                No social wall items found.
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $socialWallItems->links() }}
        </div>
    </div>

    <!-- Modal for adding/editing Social Media Type -->
    @if ($showTypeModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-show="$wire.showTypeModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md" @click.away="$wire.showTypeModal = false">
            <h2 class="text-xl font-bold mb-4">{{ $editingType ? 'Edit' : 'Add' }} Social Media Type</h2>
            <form wire:submit.prevent="saveType">
                <div class="mb-4">
                    <label for="newTypeName" class="block text-sm font-medium text-gray-700">Type Name</label>
                    <input type="text" id="newTypeName" wire:model.defer="newTypeName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('newTypeName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="newTypeIconClass" class="block text-sm font-medium text-gray-700">Font Awesome Icon Class</label>
                    <input type="text" id="newTypeIconClass" wire:model.defer="newTypeIconClass" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g., fa-brands fa-twitter">
                    <p class="text-sm text-gray-500 mt-1">Find icons and copy the class from <a href="https://fontawesome.com/search?m=free" target="_blank" class="text-blue-500 hover:underline">Font Awesome</a>.</p>
                    @error('newTypeIconClass') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" wire:click="$set('showTypeModal', false)" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Save Type</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal for adding/editing Social Wall Item -->
    @if ($showItemModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-show="$wire.showItemModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md" @click.away="$wire.showItemModal = false">
            <h2 class="text-xl font-bold mb-4">{{ $editingItem ? 'Edit' : 'Add' }} Social Wall Item</h2>
            <form wire:submit.prevent="saveItem">
                <div class="mb-4">
                    <label for="newItemSocialMediaTypeId" class="block text-sm font-medium text-gray-700">Social Media Type</label>
                    <select id="newItemSocialMediaTypeId" wire:model.defer="newItemSocialMediaTypeId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Select a type</option>
                        @foreach ($socialMediaTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('newItemSocialMediaTypeId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="newItemEmbedCode" class="block text-sm font-medium text-gray-700">Embed Code</label>
                    <textarea id="newItemEmbedCode" wire:model.defer="newItemEmbedCode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="6"></textarea>
                    @error('newItemEmbedCode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" wire:click="$set('showItemModal', false)" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md">Save Item</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        window.addEventListener('swal:confirm', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {

                    // --- TAMBAHKAN LOGGING UNTUK DEBUG ---
                    console.log('Event Method:', event.detail.method);
                    console.log('Event ID:', event.detail.id);
                    // --- BATAS LOGGING ---

                    // Pastikan ID-nya ada sebelum mengirim
                    if (event.detail.id) {
                        // Ini adalah sintaks Livewire 3 yang sudah kita perbaiki sebelumnya
                        window.Livewire.dispatch(event.detail.method, {
                            id: event.detail.id
                        });
                    } else {
                        console.error('Delete failed: ID is missing.');
                        Swal.fire('Error', 'Could not delete item. ID is missing.', 'error');
                    }
                }
            });
        });

        // Pastikan Anda juga memiliki listener untuk toast
        window.addEventListener('swal:toast', event => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: event.detail.type,
                title: event.detail.message
            });
        });
    });
</script>
@endpush