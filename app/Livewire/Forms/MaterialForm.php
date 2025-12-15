<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Rule;
use Livewire\Form;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class MaterialForm extends Form
{
    public ?User $user;

    #[Rule('nullable|url|max:255')]
    public $document_link = '';

    #[Rule('nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:20480')]
    public $document;

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->fill($user->only(['document_link']));
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->document) {
            if ($this->user->document_path) Storage::disk('public')->delete($this->user->document_path);
            $validated['document_path'] = $this->document->store('documents', 'public');
        }

        $this->user->update($validated);
        $this->setUser($this->user->fresh());
    }

    public function removeDocument()
    {
        if ($this->user->document_path) {
            Storage::disk('public')->delete($this->user->document_path);
            $this->user->update(['document_path' => null]);
            $this->setUser($this->user->fresh());
        }
    }
}
