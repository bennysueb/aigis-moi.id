<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                    @endif

                    <div class="mb-4">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Search by name or email..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>



                    <table class="min-w-full divide-y divide-gray-200 responsive-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-normal">
                                    @foreach($user->roles as $role)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-200 text-blue-800">
                                        {{ $role->name }}
                                    </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if ($user->id !== 1)
                                    <a href="{{ route('admin.users.edit', $user) }}" wire:navigate
                                        class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-900">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        <span>Edit User</span>
                                    </a>

                                    <button wire:click="edit({{ $user->id }})"
                                        class="inline-flex items-center space-x-1 text-indigo-600 hover:text-indigo-900 ml-4">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01-.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106A1.532 1.532 0 0111.49 3.17zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Edit Roles</span>
                                    </button>

                                    <button @click.prevent="
                                                    Swal.fire({
                                                        title: 'Are you sure?',
                                                        text: 'You won\'t be able to revert this!',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#d33',
                                                        cancelButtonColor: '#3085d6',
                                                        confirmButtonText: 'Yes, delete it!'
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            $dispatch('delete-user', { userId: {{ $user->id }} })
                                                        }
                                                    })
                                                "
                                        class="inline-flex items-center space-x-1 text-red-600 hover:text-red-900 ml-4">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Delete</span>
                                    </button>
                                    @else
                                    <span class="text-gray-400 font-medium">Cannot be edited</span>
                                    @endif
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($showModal)
    <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 bg-gray-500 opacity-75"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="updateUserRoles">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Roles for {{ $name }}</h3>
                        <p class="text-sm text-gray-600">{{ $email }}</p>

                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-900">Assign Roles</h4>
                            <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($allRoles as $role)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model.defer="assignedRoles" value="{{ $role->name }}" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                    <span class="ml-2 text-sm text-gray-600">{{ $role->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 sm:ml-3 sm:w-auto sm:text-sm">Save Changes</button>
                        <button type="button" wire:click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('user-deleted', (event) => {
                Swal.fire({
                    title: 'Deleted!',
                    text: event.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                });
            });

            Livewire.on('delete-failed', (event) => {
                Swal.fire({
                    title: 'Oops!',
                    text: event.message,
                    icon: 'error',
                });
            });
        });
    </script>
    @endpush
</div>