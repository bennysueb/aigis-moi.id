<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

// Gunakan layout untuk tamu (tanpa navigasi user yang login)
#[Layout('layouts.guest')]
class Register extends Component
{
    // Properti untuk menampung data dari form
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Fungsi yang akan dijalankan saat form disubmit.
     */
    public function register()
    {
        // Validasi input dari pengguna
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Buat user baru di database
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']), // Enkripsi password
        ]);

        // Set session flash untuk pesan sukses
        session()->flash('status', 'Akun admin berhasil dibuat silahkan login.');

        // Arahkan ke halaman login
        return $this->redirect('/login', navigate: true);
    }

    /**
     * Render komponen (menampilkan view).
     */
    public function render()
    {
        return view('livewire.admin.register')->title('Register Admin - ' . config('app.name'));
    }
}
