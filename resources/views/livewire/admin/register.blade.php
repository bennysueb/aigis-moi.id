<main class="flex-grow flex flex-col items-center justify-center px-4 py-10">
    <div class="w-full sm:max-w-2xl bg-white shadow-md overflow-hidden rounded-xl p-6">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                Buat Akun Admin Baru
            </h2>
        </div>

        {{-- Kita gunakan wire:submit.prevent untuk memanggil fungsi register() di komponen PHP --}}
        <form class="space-y-6" wire:submit.prevent="register">
            {{-- Input untuk Nama --}}
            <div>
                <x-input-label for="name" :value="__('Nama')" />
                <div class="mt-2">
                    <x-text-input wire:model="name" id="name" name="name" type="text" required
                        autocomplete="name" class="block w-full" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
            </div>

            {{-- Input untuk Email --}}
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <div class="mt-2">
                    <x-text-input wire:model="email" id="email" name="email" type="email" required
                        autocomplete="email" class="block w-full" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>

            {{-- Input untuk No. HP --}}
            <div>
                <x-input-label for="phone" :value="__('Nomer HP')" />
                <div class="mt-2">
                    <x-text-input wire:model="phone" id="phone" name="phone" type="tel" required
                        class="block w-full" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
            </div>

            {{-- Input untuk Password dengan Icon Mata --}}
            <div x-data="{ show: false }">
                <x-input-label for="password" :value="__('Password')" />
                <div class="mt-2 relative">
                    <x-text-input wire:model="password" id="password" name="password"
                        ::type="show ? 'text' : 'password'" required class="block w-full" />

                    {{-- Tombol untuk Show/Hide Password --}}
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500">
                        {{-- Icons ... --}}
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.432 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L6.228 6.228" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div x-data="{ show: false }">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                <div class="mt-2 relative">
                    <x-text-input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation"
                        ::type="show ? 'text' : 'password'" required class="block w-full" />

                    {{-- Tombol untuk Show/Hide Password --}}
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500">
                        {{-- Icons ... (bisa disalin dari atas) --}}
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.432 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L6.228 6.228" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            {{-- Tombol Submit --}}
            <div>
                <x-primary-button type="submit" class="w-full flex justify-center">
                    Buat Akun
                </x-primary-button>
            </div>
        </form>

</main>