<?php

namespace App\Livewire\Public;

use App\Models\User;
use Livewire\Component;

class ExhibitorProfile extends Component
{
    public User $user; // Properti untuk menampung data exhibitor

    // Livewire akan secara otomatis meng-inject User yang ditemukan oleh rute
    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.public.exhibitor-profile')
            ->layout('layouts.app');
    }
}
