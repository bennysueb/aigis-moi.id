<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Rule;
use Livewire\Form;
use App\Models\User;

class SocialForm extends Form
{
    public ?User $user;

    #[Rule('nullable|url|regex:/^https?:\/\//i|max:255')]
    public $website = '';

    #[Rule('nullable|url|regex:/^https?:\/\//i|max:255')]
    public $linkedin = '';

    #[Rule('nullable|url|regex:/^https?:\/\//i|max:255')]
    public $instagram = '';

    #[Rule('nullable|url|regex:/^https?:\/\//i|max:255')]
    public $facebook = '';

    #[Rule('nullable|url|regex:/^https?:\/\//i|max:255')]
    public $youtube_link = '';

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->fill($user->only(['website', 'linkedin', 'instagram', 'facebook', 'youtube_link']));
    }

    public function save()
    {
        $this->user->update($this->validate());
        $this->setUser($this->user->fresh());
    }
}
