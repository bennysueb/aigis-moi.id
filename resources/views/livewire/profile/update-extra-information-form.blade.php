<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Extra Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's additional profile information.") }}
        </p>
    </header>

    <form wire:submit.prevent="updateExtraInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="phone_number" :value="__('Phone Number')" />
            <x-text-input wire:model.defer="phone_number" id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>

        <div>
            <x-input-label for="nama_instansi" :value="__('Nama Instansi')" />
            <x-text-input wire:model.defer="nama_instansi" id="nama_instansi" name="nama_instansi" type="text" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('nama_instansi')" />
        </div>

        <div>
            <x-input-label for="tipe_instansi" :value="__('Tipe Instansi')" />
            <x-text-input wire:model.defer="tipe_instansi" id="tipe_instansi" name="tipe_instansi" type="text" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('tipe_instansi')" />
        </div>

        <div>
            <x-input-label for="jabatan" :value="__('Jabatan')" />
            <x-text-input wire:model.defer="jabatan" id="jabatan" name="jabatan" type="text" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('jabatan')" />
        </div>

        <div>
            <x-input-label for="alamat" :value="__('Alamat')" />
            <textarea wire:model.defer="alamat" id="alamat" name="alamat" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="saved">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>