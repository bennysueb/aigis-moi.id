<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateExtraInformationForm extends Component
{
    public $phone_number = '';
    public $nama_instansi = '';
    public $tipe_instansi = '';
    public $jabatan = '';
    public $alamat = '';

    /**
     * Isi properti dengan data pengguna saat ini.
     */
    public function mount()
    {
        $user = Auth::user();
        $this->phone_number = $user->phone_number;
        $this->nama_instansi = $user->nama_instansi;
        $this->tipe_instansi = $user->tipe_instansi;
        $this->jabatan = $user->jabatan;
        $this->alamat = $user->alamat;
    }

    /**
     * Perbarui informasi profil pengguna.
     */
    public function updateExtraInformation()
    {
        $user = Auth::user();

        $validated = $this->validate([
            'phone_number' => ['nullable', 'string', 'max:25'],
            'nama_instansi' => ['nullable', 'string', 'max:255'],
            'tipe_instansi' => ['nullable', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
        ]);

        $user->fill($validated);
        $user->save();

        session()->flash('status', 'extra-information-updated');
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.profile.update-extra-information-form');
    }
}
