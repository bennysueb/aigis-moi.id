<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}: {{ $name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900">Profile Information</h3>
                    <p class="mt-1 text-sm text-gray-600">Update user's profile information and email address.</p>

                    @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative my-4" role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                    @endif

                    <form wire:submit.prevent="updateProfile" class="mt-6 space-y-6">
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="roles" :value="__('Roles')" />
                            <div class="mt-2 space-y-2">
                                @foreach ($allRoles as $role)
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="assignedRoles" value="{{ $role->name }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-600">{{ $role->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900">Update Password</h3>
                    <p class="mt-1 text-sm text-gray-600">Ensure the user's account is using a long, random password to stay secure.</p>

                    @if (session()->has('message-password'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative my-4" role="alert">
                        <span class="block sm:inline">{{ session('message-password') }}</span>
                    </div>
                    @endif

                    <form wire:submit.prevent="updatePassword" class="mt-6 space-y-6">
                        <div>
                            <x-input-label for="password" :value="__('New Password')" />
                            <x-text-input wire:model="password" id="password" name="password" type="password" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save Password') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>