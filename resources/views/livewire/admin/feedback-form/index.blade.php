<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Feedback Forms
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search forms..." class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Create New Form
                </button>
            </div>

            {{-- Tabel Daftar Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Questions</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($forms as $form)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $form->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ count($form->fields) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="edit({{ $form->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button wire:click="delete({{ $form->id }})" class="text-red-600 hover:text-red-900 ml-4">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No feedback forms found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $forms->links() }}</div>
        </div>
    </div>

    {{-- Modal untuk Create/Edit --}}
    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            {{ $isEditMode ? 'Edit Feedback Form' : 'Create Feedback Form' }}
        </x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700">Form Name</label>
                    <input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <hr>
                <div>
                    <h3 class="font-medium text-gray-800 mb-2">Questions</h3>
                    @foreach($fields as $index => $field)
                    <div class="p-4 border rounded-md mb-3 space-y-2 bg-gray-50">
                        <div class="flex justify-end">
                            <button wire:click="removeField({{ $index }})" class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                        </div>
                        <div>
                            <label for="label_{{ $index }}" class="block font-medium text-sm text-gray-700">Question Label</label>
                            <input id="label_{{ $index }}" type="text" class="mt-1 block w-full" wire:model.defer="fields.{{ $index }}.label">
                            @error('fields.'.$index.'.label') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="name_{{ $index }}" class="block font-medium text-sm text-gray-700">Question Name (Unique Key)</label>
                            <input id="name_{{ $index }}" type="text" class="mt-1 block w-full" wire:model.defer="fields.{{ $index }}.name" placeholder="e.g., 'kepuasan_materi'">
                            @error('fields.'.$index.'.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="type_{{ $index }}" class="block font-medium text-sm text-gray-700">Question Type</label>
                            <select id="type_{{ $index }}" class="mt-1 block w-full" wire:model.live="fields.{{ $index }}.type">
                                <option value="text">Text (Single Line)</option>
                                <option value="textarea">Textarea (Multi Line)</option>
                                <option value="rating">Rating (1-5)</option>
                                <option value="select">Select (Dropdown)</option>
                                <option value="radio">Radio Button</option>
                            </select>
                        </div>

                        @if(in_array($field['type'], ['select', 'radio']))
                        <div class="mt-2">
                            <label for="options_{{ $index }}" class="block font-medium text-sm text-gray-700">Options</label>
                            <input id="options_{{ $index }}" type="text" class="mt-1 block w-full" wire:model.defer="fields.{{ $index }}.options" placeholder="e.g., Option 1,Option 2,Option 3">
                            <small class="text-xs text-gray-500">Separate options with a comma (,).</small>
                            @error('fields.'.$index.'.options') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        <div class="mt-2">
                            <label for="required_{{ $index }}" class="flex items-center">
                                <input id="required_{{ $index }}" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" wire:model.defer="fields.{{ $index }}.required">
                                <span class="ml-2 text-sm text-gray-600">Is this question required?</span>
                            </label>
                        </div>

                    </div>
                    @endforeach
                    <button wire:click="addField" type="button" class="text-sm text-blue-600 hover:text-blue-800">+ Add Question</button>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button wire:click="closeModal" class="mr-2">Cancel</button>
            <button wire:click="save" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </x-slot>
    </x-dialog-modal>
</div>