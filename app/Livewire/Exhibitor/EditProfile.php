<?php

namespace App\Livewire\Exhibitor;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Forms\InstansiForm;
use App\Livewire\Forms\ContactForm;
use App\Livewire\Forms\SocialForm;
use App\Livewire\Forms\MaterialForm;
use App\Livewire\Forms\PasswordForm;


class EditProfile extends Component
{
    use WithFileUploads;

    public InstansiForm $instansiForm;
    public ContactForm $contactForm;
    public SocialForm $socialForm;
    public MaterialForm $materialForm;
    public PasswordForm $passwordForm;

    public bool $showConfirmModal = false;
    public string $actionToConfirm = '';

    public function mount()
    {
        $user = Auth::user();
        $this->instansiForm->setUser($user);
        $this->contactForm->setUser($user);
        $this->socialForm->setUser($user);
        $this->materialForm->setUser($user);
        $this->passwordForm->setUser($user);
    }

    public function saveInstansi()
    {
        $this->instansiForm->save();
        $this->dispatch('saved-instansi');
    }

    public function saveContact()
    {
        $this->contactForm->save();
        $this->dispatch('saved-kontak');
    }

    public function saveSocial()
    {
        $this->socialForm->save();
        $this->dispatch('saved-medsos');
    }

    public function saveMaterial()
    {
        $this->materialForm->save();
        $this->dispatch('saved-materi');
    }

    public function updatePassword()
    {
        $this->passwordForm->save();
        $this->dispatch('saved-password');
    }

    public function removeLogo()
    {
        $this->instansiForm->removeLogo();
        // Reset properti upload file agar tidak error
        $this->instansiForm->logo = null;
    }

    public function removeDocument()
    {
        $this->materialForm->removeDocument();
        // Reset properti upload file agar tidak error
        $this->materialForm->document = null;
    }

    public function executeDeletion()
    {
        if ($this->actionToConfirm === 'removeLogo') {
            $this->removeLogo();
        }

        if ($this->actionToConfirm === 'removeDocument') {
            $this->removeDocument();
        }

        $this->showConfirmModal = false;
        $this->actionToConfirm = '';
    }

    public function render()
    {
        return view('livewire.exhibitor.edit-profile')->layout('layouts.app');
    }
}
