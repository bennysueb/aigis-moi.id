<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Custom Inquiry Forms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4">
                        <button wire:click="create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create New Form
                        </button>
                    </div>
                    @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('message') }}
                    </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Form Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($forms as $form)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $form->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $form->slug }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('forms.results.show', $form->slug) }}" target="_blank" class="text-purple-600 hover:text-purple-900 mr-4">
                                            View Public Results
                                        </a>
                                        <a href="{{ route('forms.show', $form) }}" target="_blank" class="text-green-600 hover:text-green-900 mr-4">View</a>
                                        <button wire:click="edit({{ $form->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <button wire:click="delete({{ $form->id }})" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900 ml-4">Delete</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">No forms created yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($showModal)
    <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 bg-gray-500 opacity-75"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-4xl sm:w-full">
                <form wire:submit.prevent="save">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $isEditMode ? 'Edit Form' : 'Create Form' }}</h3>
                        <div class="mt-4">
                            <label for="name">Form Name</label>
                            <input type="text" wire:model.defer="name" id="name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-900">Fields</h4>
                            <div class="space-y-4 mt-2">
                                @foreach($fields as $index => $field)
                                <div class="p-3 bg-gray-50 rounded-md border">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-grow">
                                            {{-- ▼▼ PERUBAHAN 1: Label dinamis untuk input teks ▼▼ --}}
                                            @if(in_array($field['type'], ['heading', 'paragraph']))
                                            <label class="text-xs text-gray-500">{{ $field['type'] === 'heading' ? 'Heading Text' : 'Paragraph Text' }}</label>
                                            <input type="text" wire:model="fields.{{ $index }}.label" class="w-full text-sm border-gray-300 rounded-md">
                                            @else
                                            <label class="text-xs text-gray-500">Field Label</label>
                                            <input type="text" wire:model="fields.{{ $index }}.label" class="w-full text-sm border-gray-300 rounded-md">
                                            @endif
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500">Field Type</label>
                                            <select wire:model.live="fields.{{ $index }}.type" class="w-full text-sm border-gray-300 rounded-md">
                                                <option value="text">Text</option>
                                                <option value="email">Email</option>
                                                <option value="textarea">Textarea</option>
                                                <option value="number">Number</option>
                                                <option value="date">Date</option>
                                                <option value="select">Select (Dropdown)</option>
                                                <option value="radio">Radio Button</option>
                                                <option value="checkbox-multiple">Multiple Choice (Checkbox)</option>
                                                <option value="checkbox">Checkbox</option>
                                                <option value="file">File Upload</option>
                                                <option value="image">Image Upload</option>
                                                <option value="signature">Signature Pad</option>
                                                <option value="heading">Heading/Title</option>
                                                <option value="paragraph">Paragraph/Description</option>
                                            </select>
                                        </div>
                                        <div class="pt-4">
                                            @if(!in_array($field['type'], ['heading', 'paragraph']))
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" wire:model="fields.{{ $index }}.required" class="rounded border-gray-300">
                                                <span class="ml-2 text-sm">Required</span>
                                            </label>
                                            @endif
                                        </div>
                                        <div>
                                            <button type="button" wire:click="removeField({{ $index }})" class="mt-4 text-red-500 hover:text-red-700">Remove</button>
                                        </div>
                                    </div>
                                    @if(in_array($field['type'], ['select', 'radio', 'checkbox-multiple']))
                                    <div class="mt-2">
                                        <label class="text-xs text-gray-500">Options (comma-separated)</label>
                                        <input type="text" wire:model="fields.{{ $index }}.options" class="w-full text-sm border-gray-300 rounded-md" placeholder="e.g. Option A, Option B, Option C">
                                    </div>
                                    <div class="mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model="fields.{{ $index }}.enable_slot_validation" class="rounded border-gray-300">
                                            <span class="ml-2 text-sm">Jadikan Pilihan Unik (Cegah Duplikasi/Booking)</span>
                                        </label>
                                    </div>
                                    @endif

                                </div>
                                @endforeach
                            </div>
                            @error('fields.*.label') <div class="text-red-500 text-xs mt-2">{{ $message }}</div> @enderror
                            <button type="button" wire:click="addField" class="mt-4 text-sm text-blue-600 hover:text-blue-800">+ Add Field</button>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" ...>Save</button>
                        <button type="button" wire:click="closeModal()" ...>Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>