<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">
                Manajemen Voucher & Diskon
            </h1>
            <x-primary-button wire:click="create">
                + Buat Voucher
            </x-primary-button>
        </div>
        <div class="mt-6">
            <x-text-input wire:model.live.debounce.300ms="search" placeholder="Cari Kode Voucher..." class="w-full md:w-1/3" />
        </div>
    </div>

    <div class="p-6 lg:p-8 bg-white border-b border-gray-200"></div>
    <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-4 inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    {{-- Tabel Voucher --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe & Nilai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kuota (Terpakai)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masa Berlaku</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($vouchers as $voucher)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono font-bold text-blue-600 text-lg">{{ $voucher->code }}</span>
                                        @if($voucher->min_purchase_amount > 0)
                                        <div class="text-xs text-gray-500 mt-1">Min. Belanja: Rp {{ number_format($voucher->min_purchase_amount, 0, ',', '.') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($voucher->type == 'percentage')
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">Diskon {{ $voucher->amount }}%</span>
                                        @else
                                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Potongan Rp {{ number_format($voucher->amount, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <span class="font-bold">{{ $voucher->usages_count }}</span> / {{ $voucher->usage_limit }}
                                        <div class="text-xs text-gray-400">Max {{ $voucher->usage_per_user }}x per user</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="text-xs">Mulai: {{ $voucher->valid_from ? $voucher->valid_from->format('d M Y') : '-' }}</div>
                                        <div class="text-xs text-red-500">Sampai: {{ $voucher->valid_until ? $voucher->valid_until->format('d M Y') : 'Selamanya' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button wire:click="toggleStatus({{ $voucher->id }})"
                                            class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none {{ $voucher->is_active ? 'bg-green-600' : 'bg-gray-200' }}">
                                            <span class="translate-x-1 inline-block w-4 h-4 transform bg-white rounded-full transition-transform {{ $voucher->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="edit({{ $voucher->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                        <button wire:click="delete({{ $voucher->id }})" onclick="confirm('Yakin ingin menghapus voucher ini?') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada voucher yang dibuat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $vouchers->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL FORM --}}
        <x-dialog-modal wire:model.live="showModal">
            <x-slot name="title">
                {{ $isEditMode ? 'Edit Voucher' : 'Buat Voucher Baru' }}
            </x-slot>

            <x-slot name="content">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Kode --}}
                    <div class="col-span-2">
                        <x-input-label for="code" value="Kode Voucher (Unik)" />
                        <x-text-input wire:model="code" id="code" class="block mt-1 w-full uppercase font-mono" placeholder="CONTOH: DISKON50" />
                        {{-- PERBAIKAN DI SINI --}}
                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                    </div>

                    {{-- Tipe --}}
                    <div>
                        <x-input-label for="type" value="Tipe Potongan" />
                        <select wire:model.live="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="fixed_amount">Nominal (Rp)</option>
                            <option value="percentage">Persentase (%)</option>
                        </select>
                        {{-- PERBAIKAN DI SINI --}}
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    {{-- Nilai --}}
                    <div>
                        <x-input-label for="amount" value="Nilai Potongan" />
                        <x-text-input wire:model="amount" type="number" class="block mt-1 w-full" />
                        <p class="text-xs text-gray-500 mt-1">{{ $type == 'percentage' ? 'Contoh: 10 untuk 10%' : 'Contoh: 50000 untuk Rp 50.000' }}</p>
                        {{-- PERBAIKAN DI SINI --}}
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    {{-- Min Belanja --}}
                    <div class="col-span-2">
                        <x-input-label for="min_purchase_amount" value="Minimal Total Belanja (Rp)" />
                        <x-text-input wire:model="min_purchase_amount" type="number" class="block mt-1 w-full" />
                        {{-- PERBAIKAN DI SINI --}}
                        <x-input-error :messages="$errors->get('min_purchase_amount')" class="mt-2" />
                    </div>

                    {{-- Kuota Global --}}
                    <div>
                        <x-input-label for="usage_limit" value="Kuota Total (Global)" />
                        <x-text-input wire:model="usage_limit" type="number" class="block mt-1 w-full" />
                        {{-- PERBAIKAN DI SINI --}}
                        <x-input-error :messages="$errors->get('usage_limit')" class="mt-2" />
                    </div>

                    {{-- Kuota Per User --}}
                    <div>
                        <x-input-label for="usage_per_user" value="Max Pemakaian Per User" />
                        <x-text-input wire:model="usage_per_user" type="number" class="block mt-1 w-full" />
                        {{-- PERBAIKAN DI SINI --}}
                        <x-input-error :messages="$errors->get('usage_per_user')" class="mt-2" />
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div>
                        <x-input-label for="valid_from" value="Berlaku Mulai" />
                        <input wire:model="valid_from" type="datetime-local" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        {{-- PERBAIKAN DI SINI --}}
                        <x-input-error :messages="$errors->get('valid_from')" class="mt-2" />
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div>
                        <x-input-label for="valid_until" value="Berlaku Sampai" />
                        <input wire:model="valid_until" type="datetime-local" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        {{-- PERBAIKAN DI SINI --}}
                        <x-input-error :messages="$errors->get('valid_until')" class="mt-2" />
                    </div>

                    {{-- Aktif Checkbox --}}
                    <div class="col-span-2 flex items-center mt-2">
                        <label for="is_active" class="inline-flex items-center">
                            <input wire:model="is_active" id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600">Aktifkan Voucher ini sekarang?</span>
                        </label>
                    </div>

                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$set('showModal', false)" wire:loading.attr="disabled">
                    Batal
                </x-secondary-button>

                <x-primary-button class="ml-2" wire:click="save" wire:loading.attr="disabled">
                    {{ $isEditMode ? 'Simpan Perubahan' : 'Buat Voucher' }}
                </x-primary-button>
            </x-slot>
        </x-dialog-modal>

        {{-- SweetAlert Script --}}
        <script>
            document.addEventListener('livewire:initialized', () => {
                @this.on('swal:success', (event) => {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    Toast.fire({
                        icon: 'success',
                        title: event.message
                    });
                });
            });
        </script>
    </div>
</div>