<?php

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    // User Info
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Tenant Info
    public string $shop_name = '';
    public string $phone_number = '';
    public string $address = '';
    public string $description = '';

    // Bank Info
    public string $bank_name = '';
    public string $bank_account = '';
    public string $bank_holder = '';

    public function registerTenant(): void
    {
        $validated = $this->validate([
            // User Validation
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],

            // Tenant Validation
            'shop_name' => ['required', 'string', 'max:255', 'unique:tenants,name'],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],

            // Bank Validation
            'bank_name' => ['required', 'string'],
            'bank_account' => ['required', 'numeric'],
            'bank_holder' => ['required', 'string'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // 1. Create User
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'phone_number' => $this->phone_number,
                ]);

                // Assign Role (Opsional, jika menggunakan Spatie)
                $user->assignRole('Tenant');

                // 2. Create Tenant Profile
                Tenant::create([
                    'user_id' => $user->id,
                    'name' => $this->shop_name,
                    'slug' => \Illuminate\Support\Str::slug($this->shop_name),
                    'phone_number' => $this->phone_number,
                    'address' => $this->address,
                    'description' => $this->description,
                    'bank_name' => $this->bank_name,
                    'bank_account' => $this->bank_account,
                    'bank_holder' => $this->bank_holder,
                    'status' => 'active', // Bisa ubah ke 'pending' jika butuh approval
                    'balance' => 0
                ]);

                // 3. Login
                Auth::login($user);
            });

            // Redirect to Tenant Dashboard (Product Manager)
            $this->redirect(route('tenant.products'), navigate: true);
        } catch (\Exception $e) {
            $this->addError('email', 'Terjadi kesalahan saat mendaftar: ' . $e->getMessage());
        }
    }
}; ?>

<main class="flex-grow flex flex-col items-center justify-center px-4 py-10">
    <div class="w-full sm:max-w-2xl bg-white shadow-md overflow-hidden rounded-xl p-6">

        <div class="flex flex-col items-center mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mt-4 mb-2 text-center uppercase">
                {{ __('auth.register') }}
            </h2>
        </div>

        <form wire:submit="registerTenant">

            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Informasi Akun</h3>

                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap Owner')" />
                    <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                    <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="space-y-4 mt-8">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Profil Toko</h3>

                <div>
                    <x-input-label for="shop_name" :value="__('Nama Toko / Brand')" />
                    <x-text-input wire:model="shop_name" id="shop_name" class="block mt-1 w-full" type="text" required placeholder="Contoh: Kopi Kenangan" />
                    <x-input-error :messages="$errors->get('shop_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Deskripsi Singkat')" />
                    <textarea wire:model="description" id="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone_number" :value="__('No. WhatsApp Bisnis')" />
                    <x-text-input wire:model="phone_number" id="phone_number" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="address" :value="__('Alamat Operasional')" />
                    <textarea wire:model="address" id="address" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="2"></textarea>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
            </div>

            <div class="space-y-4 mt-8 bg-blue-50 p-4 rounded-lg border border-blue-100">
                <h3 class="text-lg font-medium text-blue-900 border-b border-blue-200 pb-2">Rekening Pencairan Dana</h3>
                <p class="text-xs text-blue-600">Rekening ini digunakan untuk menerima hasil penjualan.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="bank_name" :value="__('Nama Bank')" />
                        <select wire:model="bank_name" id="bank_name" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Pilih Bank</option>
                            <option value="BCA">BCA</option>
                            <option value="BNI">BNI</option>
                            <option value="BRI">BRI</option>
                            <option value="MANDIRI">Mandiri</option>
                            <option value="JAGO">Bank Jago</option>
                            <option value="LAINNYA">Lainnya</option>
                        </select>
                        <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="bank_account" :value="__('Nomor Rekening')" />
                        <x-text-input wire:model="bank_account" id="bank_account" class="block mt-1 w-full" type="text" required />
                        <x-input-error :messages="$errors->get('bank_account')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="bank_holder" :value="__('Atas Nama')" />
                    <x-text-input wire:model="bank_holder" id="bank_holder" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('bank_holder')" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center justify-end mt-8">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                    {{ __('Sudah terdaftar? Login') }}
                </a>

                <x-primary-button class="ms-4" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __('Daftar Tenant') }}</span>
                    <span wire:loading>{{ __('Memproses...') }}</span>
                </x-primary-button>
            </div>
        </form>
    </div>
</main>