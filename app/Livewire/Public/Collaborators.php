<?php

namespace App\Livewire\Public;

use App\Models\CollaboratorCategory;
use Livewire\Component;

class Collaborators extends Component
{
    public function render()
    {
        // Ambil kategori yang aktif, urutkan sesuai sort_order
        // Eager load collaborators yang aktif juga, diurutkan sesuai sort_order
        $categories = CollaboratorCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->with(['collaborators' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->get();

        // Menggunakan layout 'guest' karena ini halaman publik (tanpa login)
        // Jika Anda punya layout khusus public lain, silakan ganti 'layouts.guest'
        return view('livewire.public.collaborators', [
            'categories' => $categories
        ])->layout('layouts.guest');
    }
}
