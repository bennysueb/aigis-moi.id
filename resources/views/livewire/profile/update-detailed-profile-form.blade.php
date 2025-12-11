<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('auth.Personal_Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('auth.Update_your_profile') }}
        </p>
    </header>

    <form wire:submit="updateDetailedProfile" class="mt-6 space-y-6">

        {{-- BARIS 1: Personal Info --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            {{-- Umur / Age --}}
            <div>
                <x-input-label for="date_of_birth" value="{{ __('auth.age') }}" />
                <x-text-input wire:model="state.date_of_birth" id="date_of_birth" type="number" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('state.date_of_birth')" />
            </div>
            {{-- Gender --}}
            <div>
                <x-input-label for="gender" value="{{ __('auth.Gender') }}" />
                <select wire:model="state.gender" id="gender" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">{{ __('auth.Choose') }}</option>
                    <option value="Male">{{ __('auth.Male') }}</option>
                    <option value="Female">{{ __('auth.Female') }}</option>
                    <option value="Prefer not to say">{{ __('auth.Prefer_not_to_say') }}</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('state.gender')" />
            </div>
        </div>

        {{-- BARIS 2: Domisili Personal --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <x-input-label for="country" value="{{ __('auth.Country') }}" />
                <select wire:model="state.country" id="country" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">{{ __('auth.Choose') }}</option>
                    @foreach ( (new PragmaRX\Countries\Package\Countries)->all()->sortBy('name.common') as $country)
                    <option value="{{ $country->name->common }}">{{ $country->name->common }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('state.country')" />
            </div>
            <div>
                <x-input-label for="city" value="{{ __('auth.City_of_Residence') }}" />
                <x-text-input wire:model="state.city" id="city" type="text" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('state.city')" />
            </div>
        </div>

        {{-- BAGIAN BARU: Informasi Institusi --}}
        <div class="pt-6 border-t">
            <h3 class="text-md font-medium text-gray-800 mb-4">{{ __('auth.institution_information') }}</h3>

            {{-- Kategori Institusi --}}
            <div class="mt-4">
                <x-input-label for="type_institution" value="{{ __('auth.institution_category') }}" />
                <select wire:model.live="state.type_institution" id="type_institution" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">{{ __('auth.Choose') }}</option>
                    <option value="Government">Government</option>
                    <option value="Company">Company</option>
                    <option value="Association">Association</option>
                    <option value="NGOs">Non-Government Organizations (NGOs)</option>
                    <option value="Academic">Academic</option>
                    <option value="Media">Media</option>
                    <option value="Other">Other</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('state.type_institution')" />
            </div>

            {{-- Other Institution (Muncul jika Other dipilih) --}}
            @if($state['type_institution'] === 'Other')
            <div class="mt-4">
                <x-input-label for="other_institution" value="{{ __('auth.please_specify') }}" />
                <x-text-input wire:model="state.other_institution" id="other_institution" class="mt-1 block w-full" type="text" />
                <x-input-error class="mt-2" :messages="$errors->get('state.other_institution')" />
            </div>
            @endif

            {{-- Media Type (Muncul jika Media dipilih) --}}
            @if($state['type_institution'] === 'Media')
            <div class="mt-4 space-y-2">
                <x-input-label value="{{ __('auth.media_type') }}" />
                @foreach(['Newspaper', 'Magazine', 'Radio', 'Tabloid', 'Television', 'Online'] as $media)
                <label class="flex items-center">
                    <input wire:model="state.media_type" type="radio" value="{{ $media }}" class="text-indigo-600 focus:ring-indigo-500">
                    <span class="ms-2 text-sm text-gray-600">{{ $media }}</span>
                </label>
                @endforeach
                <x-input-error class="mt-2" :messages="$errors->get('state.media_type')" />
            </div>
            @endif

            {{-- Nama Institusi & Jabatan --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                <div>
                    <x-input-label for="institution" value="{{ __('auth.institution_name') }}" />
                    <x-text-input wire:model="state.institution" id="institution" class="mt-1 block w-full" type="text" />
                    <x-input-error class="mt-2" :messages="$errors->get('state.institution')" />
                </div>
                <div>
                    <x-input-label for="occupation" value="{{ __('auth.position') }}" />
                    <x-text-input wire:model="state.occupation" id="occupation" class="mt-1 block w-full" type="text" />
                    <x-input-error class="mt-2" :messages="$errors->get('state.occupation')" />
                </div>
            </div>

            {{-- Lokasi Institusi (Dropdown Bertingkat) --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-4">
                {{-- Negara --}}
                <div>
                    <x-input-label for="institution_country_id" value="{{ __('auth.Country') }} (Institution)" />
                    <select wire:model.live="institution_country_id" id="institution_country_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('auth.Choose') }}</option>
                        @foreach ($allCountries as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('institution_country_id')" />
                </div>

                {{-- Provinsi --}}
                <div>
                    <x-input-label for="institution_state_id" value="{{ __('auth.State') }} (Institution)" />
                    <div wire:loading wire:target="institution_country_id" class="text-xs text-gray-500 mb-1">Loading...</div>
                    <select wire:model.live="institution_state_id" id="institution_state_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm disabled:bg-gray-100" @disabled($allStates->isEmpty())>
                        <option value="">{{ __('auth.Choose') }}</option>
                        @foreach ($allStates as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('institution_state_id')" />
                </div>

                {{-- Kota --}}
                <div>
                    <x-input-label for="institution_city_id" value="{{ __('auth.City') }} (Institution)" />
                    <div wire:loading wire:target="institution_state_id" class="text-xs text-gray-500 mb-1">Loading...</div>
                    <select wire:model.live="institution_city_id" id="institution_city_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm disabled:bg-gray-100" @disabled($allCities->isEmpty())>
                        <option value="">{{ __('auth.Choose') }}</option>
                        @foreach ($allCities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('institution_city_id')" />
                </div>
            </div>
        </div>

        {{-- Sumber Informasi --}}
        <div class="pt-6 border-t">
            <h3 class="text-md font-medium text-gray-800 mb-4">{{ __('auth.Account_Information_Consent') }}</h3>
            <div class="mt-4">
                <x-input-label for="info_source" value="{{ __('auth.info_source') }}" />
                <select wire:model.live="state.info_source" id="info_source" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">{{ __('auth.Choose') }}</option>
                    <option value="Social Media">{{ __('auth.Social_Media') }}</option>
                    <option value="Website">{{ __('auth.Website') }}</option>
                    <option value="Friends/Family">{{ __('auth.Friends_Family') }}</option>
                    <option value="School/University">{{ __('auth.School_University') }}</option>
                    <option value="Other">{{ __('auth.Other') }}</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('state.info_source')" />

                @if($state['info_source'] === 'Other')
                <div class="mt-4">
                    <x-input-label for="info_source_other" value="{{ __('auth.please_specify') }}" />
                    <x-text-input
                        wire:model="state.info_source_other"
                        id="info_source_other"
                        class="mt-1 block w-full"
                        type="text"
                        placeholder="Please specify..."
                        autocomplete="off" />
                    <x-input-error class="mt-2" :messages="$errors->get('state.info_source_other')" />
                </div>
                @endif
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div class="flex items-center gap-4">
            <x-primary-button wire:loading.attr="disabled">{{ __('Save') }}</x-primary-button>
            <x-action-message class="me-3" on="saved">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>