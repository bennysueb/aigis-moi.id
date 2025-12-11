<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Rule;
use Livewire\Form;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class InstansiForm extends Form
{
    public ?User $user;

    #[Rule('required|string|max:255')]
    public $nama_instansi = '';

    #[Rule('nullable|string|max:255')]
    public $booth_number = '';

    #[Rule('nullable|string|max:255')]
    public $tipe_instansi = '';

    #[Rule('nullable|string|max:25')]
    public $phone_instansi = '';

    #[Rule('nullable|string|max:1000')]
    public $description = '';

    #[Rule('nullable|string')]
    public $alamat = '';

    #[Rule('nullable|image|max:1024')]
    public $logo;

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->fill($user->only(['nama_instansi', 'tipe_instansi', 'booth_number', 'phone_instansi', 'description', 'alamat']));
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->logo) {
            if ($this->user->logo_path) Storage::disk('public')->delete($this->user->logo_path);
            $validated['logo_path'] = $this->logo->store('logos', 'public');
        }

        $this->user->update($validated);
        $this->setUser($this->user->fresh());
    }

    public function removeLogo()
    {
        if ($this->user->logo_path) {
            Storage::disk('public')->delete($this->user->logo_path);
            $this->user->update(['logo_path' => null]);
            $this->setUser($this->user->fresh());
        }
    }
}
