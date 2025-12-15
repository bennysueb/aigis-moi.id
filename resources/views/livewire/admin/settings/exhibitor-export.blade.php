<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Ekspor Exhibitor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <form wire:submit.prevent="save">
                    {{-- Bagian Kolom Standar --}}
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Pilih Kolom Standar
                    </h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Kolom ini akan selalu tersedia untuk diekspor.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($standardColumns as $key => $label)
                        <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                            <input type="checkbox" wire:model.live="selectedColumns" value="{{ $key }}" id="std-{{ $key }}" class="form-checkbox h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500">
                            <span class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>

                    <hr class="my-8">

                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Pilih Kolom dari Profil Detail Pengguna
                    </h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Kolom ini berasal dari data profil detail yang diisi oleh pengguna.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($profileDataColumns as $key => $label)
                        <label class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200 hover:bg-green-100 transition">
                            <input type="checkbox" wire:model.live="selectedColumns" value="{{ $key }}" id="prof-{{ $key }}" class="form-checkbox h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500">
                            <span class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>

                    <hr class="my-8">

                    {{-- Bagian Kolom Dinamis --}}
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Pilih Kolom Kustom dari Event
                    </h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Pilih event untuk menampilkan kolom tambahan dari formulir pendaftaran kustom.
                    </p>

                    {{-- Dropdown Pemilih Event --}}
                    <div>
                        <x-input-label for="event" :value="__('Pilih Event')" />
                        <select wire:model.live="selectedEventId" id="event" class="mt-1 block w-full md:w-1/2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Tidak ada --</option>
                            @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Checkbox Dinamis --}}
                    @if(!empty($dynamicColumns))
                    <div class="mt-6">
                        <h4 class="text-md font-medium text-gray-800 mb-4">Kolom Kustom Ditemukan:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($dynamicColumns as $column)
                            <label class="flex items-center p-3 bg-blue-50 rounded-lg border border-blue-200 hover:bg-blue-100 transition">
                                <input type="checkbox" wire:model.live="selectedColumns" value="{{ $column }}" id="dyn-{{ $column }}" class="form-checkbox h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500">
                                <span class="ml-3 text-sm font-medium text-gray-700">{{ Str::title(str_replace('_', ' ', $column)) }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @elseif($selectedEventId)
                    <div wire:loading.remove class="mt-6 text-sm text-gray-500">
                        Tidak ada kolom kustom yang ditemukan untuk event ini.
                    </div>
                    @endif

                    <div wire:loading wire:target="selectedEventId" class="mt-6 text-sm text-gray-500">
                        <svg class="animate-spin h-5 w-5 mr-3 inline-block" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mencari kolom kustom...
                    </div>


                    {{-- Tombol Simpan --}}
                    <div class="mt-8 flex justify-end">
                        <x-primary-button>
                            {{ __('Simpan Pengaturan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>