<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public $scrolled = true;

    public function mount($scrolled = true)
    {
        $this->scrolled = $scrolled;
    }

    public function setLocale($locale)
    {
        Session::put('locale', $locale);
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}