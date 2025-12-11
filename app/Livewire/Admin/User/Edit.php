<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class Edit extends Component
{
    public User $user; // Ini akan di-injeksi secara otomatis oleh Laravel

    // Properti untuk Form Profil
    public $name;
    public $email;
    public $allRoles;
    public $assignedRoles = [];

    // Properti untuk Form Password Baru
    public $password = '';
    public $password_confirmation = '';

    /**
     * Mount (dijalankan saat komponen dimuat)
     */
    public function mount(User $user)
    {
        // PERLINDUNGAN: Pastikan tidak ada yang bisa mengedit Super Admin
        if ($user->id == 1) {
            session()->flash('error', 'Super Admin cannot be edited.');
            return $this->redirect(route('admin.users.index'), navigate: true);
        }

        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;

        // Muat semua peran dan peran yang dimiliki pengguna
        $this->allRoles = Role::all();
        $this->assignedRoles = $user->getRoleNames()->toArray();
    }

    /**
     * Simpan perubahan profil (Nama, Email, Roles)
     */
    public function updateProfile()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            // Pastikan email unik, KECUALI untuk pengguna ini sendiri
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
        ]);

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->user->syncRoles($this->assignedRoles);

        session()->flash('message', 'User profile updated successfully.');
    }

    /**
     * Simpan password baru
     */
    public function updatePassword()
    {
        $this->validate([
            'password' => ['required', 'min:8', Rules\Password::defaults(), 'confirmed'],
        ]);

        $this->user->update([
            'password' => Hash::make($this->password)
        ]);

        // Reset field password setelah disimpan
        $this->reset(['password', 'password_confirmation']);

        session()->flash('message-password', 'User password updated successfully.');
    }

    /**
     * Render tampilan
     */
    public function render()
    {
        return view('livewire.admin.user.edit')
            ->layout('layouts.app'); // Pastikan ini menggunakan layout admin Anda
    }
}
