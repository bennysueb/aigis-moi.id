<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">
                {{ __('Manage Pages') }}
            </h1>

            <div class="flex justify-end mb-4 space-x-2">
                <a href="{{ route('admin.pages.welcome-builder') }}" wire:navigate class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Manage Welcome Page
                </a>

                <a href="{{ route('admin.agenda.index') }}" wire:navigate class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Agenda Acara') }}
                </a>

                <a href="{{ route('admin.programme.index') }}" wire:navigate class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Program Acara') }}
                </a>

                <a href="{{ route('admin.collaborators.index') }}" wire:navigate class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Collaborators') }}
                </a>

                <x-primary-button wire:click="create">Create New Page</x-primary-button>
            </div>
        </div>
        <div class="mt-6">
            <x-text-input wire:model.live.debounce.300ms="search" placeholder="Search pages by title..." class="w-full md:w-1/3" />
        </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
        @endif

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <div class="overflow-x-auto">
                <div class="min-w-full align-middle"></div>
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">

                    <table class="min-w-full divide-y divide-gray-200 responsive-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title (EN)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pages as $page)
                            <tr>
                                <td data-label="Title (EN)" class="px-6 py-4 whitespace-nowrap">{{ $page->getTranslation('title', 'en') }}</td>
                                <td data-label="Slug" class="px-6 py-4 whitespace-nowrap">{{ $page->slug }}</td>
                                <td data-label="Status" class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $page->status == 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $page->status }}
                                    </span>
                                </td>
                                <td data-label="Actions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    {{-- Link Edit sudah benar, mengarah ke builder --}}
                                    <a href="{{ route('admin.pages.builder', $page->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <button wire:click="delete({{ $page->id }})" onclick="return confirm('Are you sure you want to delete this page?')" class="text-red-600 hover:text-red-900 ml-4">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $pages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>