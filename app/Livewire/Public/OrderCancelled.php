<?php

namespace App\Livewire\Public;

use App\Models\Registration;
use Livewire\Component;
use Livewire\Attributes\Layout;

class OrderCancelled extends Component
{
    public Registration $registration;

    public function mount(Registration $registration)
    {
        $this->registration = $registration;

        // Cek keamanan: Jika statusnya bukan 'canceled', lempar balik ke invoice
        if ($this->registration->status !== 'canceled') {
            return redirect()->route('invoice.show', $this->registration->uuid);
        }
    }

    public function render()
    {
        // Kita gunakan layout 'blank' agar tampilan bersih
        return view('livewire.public.order-cancelled')
            ->layout('layouts.blank', [
                'title' => 'Pesanan Dibatalkan'
            ]);
    }
}
