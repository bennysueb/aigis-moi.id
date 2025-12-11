<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Mail\ExhibitorRegistered;
use Illuminate\Support\Facades\Mail;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $nama_instansi = '';
    public string $jabatan = '';
    public string $phone_number = '';
    public string $alamat = '';

    public function registerExhibitor(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'nama_instansi' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $user->assignRole('Exhibitor');

        event(new Registered($user));

        // Kirim email notifikasi ke admin
        Mail::to(env('ADMIN_EMAIL', 'admin@example.com'))->send(new ExhibitorRegistered($user));

        Auth::login($user);

        // Hapus redirect langsung dan ganti dengan dispatch event
        $this->dispatch('registration-successful', redirectUrl: route('exhibitor.profile'));
    }
}; ?>

<main class="flex-grow flex flex-col items-center justify-center px-4 py-10">
    <div class="w-full sm:max-w-2xl bg-white shadow-md overflow-hidden rounded-xl p-6">

        <div class="flex flex-col items-center mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mt-4 mb-2 text-center uppercase">
                {{ __('auth.register') }}
            </h2>
        </div>

        <form wire:submit="registerExhibitor">
            <div>
                <x-input-label for="name" :value="__('auth.full_name')" />
                <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="email" :value="__('auth.email')" />
                <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="nama_instansi" :value="__('auth.institution_name')" />
                <x-text-input wire:model="nama_instansi" id="nama_instansi" class="block mt-1 w-full" type="text" name="nama_instansi" required />
                <x-input-error :messages="$errors->get('nama_instansi')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="jabatan" :value="__('auth.position')" />
                <x-text-input wire:model="jabatan" id="jabatan" class="block mt-1 w-full" type="text" name="jabatan" required />
                <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="phone_number" :value="__('auth.phone_number')" />
                <x-text-input wire:model="phone_number" id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" required />
                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="alamat" :value="__('auth.address')" />
                <textarea wire:model="alamat" id="alamat" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" name="alamat" required rows="3"></textarea>
                <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
            </div>

            <div class="mt-4" x-data="{ showPassword: false }">
                <x-input-label for="password" :value="__('auth.password')" />
                <div class="relative">
                    {{-- Input untuk type="password" (default) --}}
                    <div x-show="!showPassword">
                        <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" required />
                    </div>
                    {{-- Input untuk type="text" (saat password ditampilkan) --}}
                    <div x-show="showPassword" style="display: none;">
                        <x-text-input wire:model="password" id="password_visible" class="block mt-1 w-full" type="text" name="password_visible" required />
                    </div>

                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                        <i class="fas fa-eye" x-show="!showPassword"></i>
                        <i class="fas fa-eye-slash" x-show="showPassword" style="display: none;"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4" x-data="{ showPassword: false }">
                <x-input-label for="password_confirmation" :value="__('auth.password_confirmation')" />
                <div class="relative">
                    {{-- Input untuk type="password" (default) --}}
                    <div x-show="!showPassword">
                        <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                    </div>
                    {{-- Input untuk type="text" (saat password ditampilkan) --}}
                    <div x-show="showPassword" style="display: none;">
                        <x-text-input wire:model="password_confirmation" id="password_confirmation_visible" class="block mt-1 w-full" type="text" name="password_confirmation_visible" required />
                    </div>

                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                        <i class="fas fa-eye" x-show="!showPassword"></i>
                        <i class="fas fa-eye-slash" x-show="showPassword" style="display: none;"></i>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-8">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}" wire:navigate>
                    {{ __('auth.already_registered') }}
                </a>

                <x-primary-button class="ms-4" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="registerExhibitor">
                        {{ __('auth.register_exhibitor') }}
                    </span>
                    <span wire:loading wire:target="registerExhibitor">
                        {{ __('auth.loading') }}
                    </span>
                </x-primary-button>
            </div>
        </form>
    </div>
</main>