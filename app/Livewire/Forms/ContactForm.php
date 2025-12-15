<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Rule;
use Livewire\Form;
use App\Models\User;
use Illuminate\Validation\Rule as ValidationRule;

class ContactForm extends Form
{
    public ?User $user;

    #[Rule('required|string|max:255')]
    public $name = '';

    public $email = '';

    #[Rule('nullable|string|max:255')]
    public $jabatan = '';

    #[Rule('nullable|string|max:25')]
    public $whatsapp = '';

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->fill($user->only(['name', 'email', 'jabatan', 'whatsapp']));
    }

    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', ValidationRule::unique(User::class)->ignore($this->user->id)],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->user->email !== $validated['email']) {
            $validated['email_verified_at'] = null;
        }

        $this->user->update($validated);
        $this->setUser($this->user->fresh());
    }
}
