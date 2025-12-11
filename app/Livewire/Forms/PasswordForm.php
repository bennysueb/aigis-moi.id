<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Rule;
use Livewire\Form;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordForm extends Form
{
    public ?User $user;

    #[Rule('required|string|current_password')]
    public string $current_password = '';

    #[Rule('required|string|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function save()
    {
        $this->validate();
        $this->user->update(['password' => Hash::make($this->password)]);
        $this->reset('current_password', 'password', 'password_confirmation');
    }
}
