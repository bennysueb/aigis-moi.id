<?php

namespace App\Livewire\Public;

use App\Models\InquiryForm;
use Livewire\Component;
use Livewire\WithPagination;

class SubmissionsResult extends Component
{
    use WithPagination;

    public InquiryForm $form;

    public function mount(InquiryForm $form)
    {
        $this->form = $form;
    }

    public function render()
    {
        $submissions = $this->form->submissions()->latest()->paginate(10);

        // Ganti 'layouts.guest' jika Anda menggunakan nama layout publik yang berbeda
        return view('livewire.public.submissions-result', [
            'submissions' => $submissions
        ])->layout('layouts.app');
    }
}
