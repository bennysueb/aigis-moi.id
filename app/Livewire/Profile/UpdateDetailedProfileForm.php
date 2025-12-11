<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
// Model untuk Institusi
use App\Models\Country as InstitutionCountry;
use App\Models\State as InstitutionState;
use App\Models\City as InstitutionCity;
use Illuminate\Database\Eloquent\Collection;

class UpdateDetailedProfileForm extends Component
{
    // Menampung seluruh data profile_data
    public $state = [];

    // Koleksi data untuk Dropdown
    public Collection $allCountries;
    public Collection $allStates;
    public Collection $allCities;

    // Property ID Sementara untuk Dropdown Bertingkat (Dependent)
    // Kita butuh ini karena di DB yang tersimpan adalah Nama (string), bukan ID.
    public ?int $institution_country_id = null;
    public ?int $institution_state_id = null;
    public ?int $institution_city_id = null;

    /**
     * Mount: Inisialisasi data saat komponen dimuat
     */
    public function mount()
    {
        $user = Auth::user();

        // 1. Gabungkan data user dengan default value agar tidak error jika key kosong
        // Strukturnya disamakan dengan form Register
        $this->state = array_merge([
            'date_of_birth' => '', // Ini Age (Umur) di register, tipe numeric
            'gender' => '',
            'country' => '',       // Negara Domisili Personal
            'city' => '',          // Kota Domisili Personal

            // Data Institusi
            'type_institution' => '',
            'other_institution' => '',
            'media_type' => '',
            'institution' => '',
            'occupation' => '',
            'institution_country' => '', // Disimpan sebagai Nama
            'institution_state' => '',   // Disimpan sebagai Nama
            'institution_city' => '',    // Disimpan sebagai Nama

            // Sumber Info
            'info_source' => '',
            'info_source_other' => '',
        ], $user->profile_data ?? []);

        // 2. Load data awal untuk dropdown negara
        $this->allCountries = InstitutionCountry::orderBy('name')->get();
        $this->allStates = new Collection();
        $this->allCities = new Collection();

        // 3. Restore logic: Ubah Nama Institusi (dari JSON) kembali ke ID untuk Dropdown
        $this->restoreDropdownsFromState();
    }

    /**
     * Mengembalikan state ID dropdown berdasarkan Nama yang tersimpan di profile_data
     */
    protected function restoreDropdownsFromState()
    {
        // Restore ID Negara
        if (!empty($this->state['institution_country'])) {
            $country = InstitutionCountry::where('name', $this->state['institution_country'])->first();
            if ($country) {
                $this->institution_country_id = $country->id;
                // Fetch states berdasarkan negara ini
                $this->allStates = InstitutionState::where('country_id', $country->id)->orderBy('name')->get();
            }
        }

        // Restore ID Provinsi
        if (!empty($this->state['institution_state']) && $this->institution_country_id) {
            $state = InstitutionState::where('name', $this->state['institution_state'])
                ->where('country_id', $this->institution_country_id)
                ->first();
            if ($state) {
                $this->institution_state_id = $state->id;
                // Fetch cities berdasarkan provinsi ini
                $this->allCities = InstitutionCity::where('state_id', $state->id)->orderBy('name')->get();
            }
        }

        // Restore ID Kota
        if (!empty($this->state['institution_city']) && $this->institution_state_id) {
            $city = InstitutionCity::where('name', $this->state['institution_city'])
                ->where('state_id', $this->institution_state_id)
                ->first();
            if ($city) {
                $this->institution_city_id = $city->id;
            }
        }
    }

    /**
     * Listener: Saat Negara Institusi diganti
     */
    public function updatedInstitutionCountryId($value)
    {
        // Ambil data states baru
        $this->allStates = InstitutionState::where('country_id', $value)->orderBy('name')->get();

        // Reset child dropdowns
        $this->institution_state_id = null;
        $this->institution_city_id = null;
        $this->allCities = new Collection();

        // Update state names (kosongkan dulu karena ID berubah)
        $this->state['institution_country'] = InstitutionCountry::find($value)?->name;
        $this->state['institution_state'] = '';
        $this->state['institution_city'] = '';
    }

    /**
     * Listener: Saat Provinsi Institusi diganti
     */
    public function updatedInstitutionStateId($value)
    {
        // Ambil data cities baru
        $this->allCities = InstitutionCity::where('state_id', $value)->orderBy('name')->get();

        // Reset city
        $this->institution_city_id = null;

        // Update nama provinsi di state
        $this->state['institution_state'] = InstitutionState::find($value)?->name;
        $this->state['institution_city'] = '';
    }

    /**
     * Listener: Saat Kota Institusi diganti
     */
    public function updatedInstitutionCityId($value)
    {
        // Update nama kota di state
        $this->state['institution_city'] = InstitutionCity::find($value)?->name;
    }

    /**
     * Simpan Perubahan
     */
    public function updateDetailedProfile()
    {
        $validCountries = (new \PragmaRX\Countries\Package\Countries)->all()->pluck('name.common')->toArray();

        $this->validate([
            'state.date_of_birth' => ['required', 'numeric'], // Sesuai register: Age (angka)
            'state.gender' => ['required', 'string', Rule::in(['Male', 'Female', 'Prefer not to say'])],
            'state.country' => ['required', 'string', Rule::in($validCountries)],
            'state.city' => ['required', 'string', 'max:100'],

            // Validasi Institusi
            'state.type_institution' => ['required', 'string', Rule::in(['Government', 'Company', 'Association', 'NGOs', 'Academic', 'Media', 'Other'])],
            'state.other_institution' => ['required_if:state.type_institution,Other', 'nullable', 'string', 'max:255'],
            'state.media_type' => ['required_if:state.type_institution,Media', 'nullable', 'string', 'max:255'],
            'state.institution' => ['required', 'string', 'max:255'],
            'state.occupation' => ['required', 'string', 'max:255'],

            // Validasi ID Lokasi Institusi
            'institution_country_id' => ['required', 'integer', 'exists:countries,id'],
            'institution_state_id' => ['required', 'integer', 'exists:states,id'],
            'institution_city_id' => ['required', 'integer', 'exists:cities,id'],

            // Validasi Sumber Info
            'state.info_source' => ['required', 'string'],
            'state.info_source_other' => ['required_if:state.info_source,Other', 'nullable', 'string', 'max:255'],
        ]);

        // Pastikan nama lokasi tersimpan (double check)
        $this->state['institution_country'] = InstitutionCountry::find($this->institution_country_id)?->name;
        $this->state['institution_state'] = InstitutionState::find($this->institution_state_id)?->name;
        $this->state['institution_city'] = InstitutionCity::find($this->institution_city_id)?->name;

        Auth::user()->forceFill([
            'profile_data' => $this->state
        ])->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.profile.update-detailed-profile-form');
    }
}
