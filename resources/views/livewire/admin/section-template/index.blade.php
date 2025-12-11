<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Section Templates Management') }}
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

                    <div class="flex justify-between items-center mb-6">
                        <div class="w-1/3">
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search templates by name..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <button wire:click="create()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Create New Template
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($templates as $template)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $template->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $template->slug }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="edit({{ $template->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <button wire:click="delete({{ $template->id }})" wire:confirm="Are you sure you want to delete this template?" class="text-red-600 hover:text-red-900 ml-4">Delete</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No templates found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $templates->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($showModal)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <form wire:submit.prevent="save">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            @if($isEditMode)
                            Edit Section Template
                            @else
                            Create New Section Template
                            @endif
                        </h3>
                        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="name" class="block text-sm font-medium text-gray-700">Template Name</label>
                                <input type="text" wire:model="name" id="name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="sm:col-span-3">
                                <label for="slug" class="block text-sm font-medium text-gray-700">Slug (auto-generated)</label>
                                <input type="text" wire:model="slug" id="slug" readonly class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-100">
                            </div>
                            <div class="sm:col-span-6">
                                <label for="html_content" class="block text-sm font-medium text-gray-700">HTML Content</label>
                                <textarea wire:model="html_content" id="html_content" rows="10" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md font-mono"></textarea>
                                @error('html_content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="sm:col-span-6">
                                <label for="css_content" class="block text-sm font-medium text-gray-700">CSS Content (Optional)</label>
                                <textarea wire:model="css_content" id="css_content" rows="5" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md font-mono"></textarea>
                            </div>

                            <div class="sm:col-span-6 border-t pt-4">
                                <h4 class="text-md font-medium text-gray-900">Editable Fields</h4>
                                <p class="text-sm text-gray-500 mb-4">Define the fields that the editor can fill in. Use @{{ $field_name }} in the HTML content.</p>
                                @foreach($fields as $index => $field)
                                <div class="grid grid-cols-12 gap-2 items-center mb-2">
                                    <div class="col-span-4">
                                        <input type="text" wire:model="fields.{{ $index }}.label" placeholder="Field Label" class="w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div class="col-span-4">
                                        <input type="text" wire:model="fields.{{ $index }}.name" placeholder="field_name (no spaces)" class="w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div class="col-span-3">
                                        <select wire:model="fields.{{ $index }}.type" class="w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="text">Text</option>
                                            <option value="textarea">Textarea</option>
                                            <option value="image">Image</option>
                                            <option value="link">Link</option>
                                            <option value="repeater">Repeater Group (JSON)</option>
                                        </select>
                                    </div>
                                    <div class="col-span-1">
                                        <button wire:click.prevent="removeField({{ $index }})" class="text-red-500 hover:text-red-700">&times;</button>
                                    </div>
                                </div>
                                @endforeach
                                <button wire:click.prevent="addField()" type="button" class="mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add Field</button>
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