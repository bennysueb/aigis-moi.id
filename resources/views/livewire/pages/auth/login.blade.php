<?php

use App\Livewire\Forms\LoginForm;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = Auth::user();

        if ($user->roles->isNotEmpty()) {
            if ($user->roles->contains('name', 'Exhibitor')) {
                $this->redirect(url: '/exhibitor/dashboard', navigate: true);
            } else {
                $this->redirect(url: '/admin/dashboard', navigate: true);
            }
        } else {
            $this->redirectIntended(default: RouteServiceProvider::HOME, navigate: true);
        }
    }
}; ?>

<main class="flex-grow flex flex-col items-center justify-center px-4 py-32">
    <div class="w-full sm:max-w-2xl bg-white shadow-md overflow-hidden rounded-xl p-6">
        <div>
            @if (session('status'))
            <div class="mt-6 w-full sm:max-w-md">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="block sm:inline">{{ session('status') }}</span>
                    </div>
                </div>
            </div>
            @endif

            <div>
                <h2 class="text-2xl font-bold text-gray-900 mt-4 mb-6 text-center uppercase">
                    {{ __('auth.login') }}
                </h2>
            </div>

            <form wire:submit="login">
                <div class="mt-8">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                </div>

                {{-- ////////////////////////////////////////////////// --}}
                {{-- //         MULAI PERUBAHAN PADA BLOK INI        // --}}
                {{-- ////////////////////////////////////////////////// --}}

                <div class="mt-8" x-data="{ show: false }">
                    <x-input-label for="password" :value="__('Password')" />

                    <div class="relative mt-1">
                        <x-text-input wire:model="form.password" id="password" class="block w-full"
                            ::type="show ? 'text' : 'password'"
                            name="password"
                            required autocomplete="current-password" />

                        {{-- Tombol untuk Show/Hide Password --}}
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500">
                            {{-- Icon Mata Terbuka --}}
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.432 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{-- Icon Mata Tertutup --}}
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L6.228 6.228" />
                            </svg>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>

                {{-- ////////////////////////////////////////////////// --}}
                {{-- //           AKHIR DARI BLOK PERUBAHAN          // --}}
                {{-- ////////////////////////////////////////////////// --}}

                <div class="block mt-4">
                    <label for="remember" class="inline-flex items-center">
                        <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('auth.remember_me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-12">
                    @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('auth.forgot_your_password') }}
                    </a>
                    @endif

                    <x-primary-button class="ms-3">
                        {{ __('auth.login') }}
                    </x-primary-button>
                </div>
            </form>

        </div>
    </div>
</main>