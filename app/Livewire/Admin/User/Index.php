<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\On;


class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public $user_id;
    public $name, $email;
    public $allRoles;
    public $assignedRoles = [];
    public $showModal = false;

    public function mount()
    {
        $this->allRoles = Role::all();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->with('roles')
            ->paginate(15);

        return view('livewire.admin.user.index', ['users' => $users])
            ->layout('layouts.app');
    }

    public function edit($id)
    {
        if ($id == 1) {
            return; // Hentikan eksekusi jika user ID adalah 1
        }

        $user = User::findOrFail($id);
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->assignedRoles = $user->getRoleNames()->toArray();
        $this->showModal = true;
    }

    public function updateUserRoles()
    {
        $user = User::findOrFail($this->user_id);
        $user->syncRoles($this->assignedRoles);

        $this->closeModal();
        session()->flash('message', 'User roles updated successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->user_id = null;
        $this->name = '';
        $this->email = '';
        $this->assignedRoles = [];
    }

    #[On('delete-user')]
    public function destroy($userId)
    {
        // Pengecekan keamanan agar user utama (ID 1) tidak bisa dihapus.
        if ($userId == 1) {
            $this->dispatch('delete-failed', message: 'Error: The main administrator cannot be deleted.');
            return;
        }

        // Cari dan hapus user
        if ($user = User::find($userId)) {
            $user->delete();
            // Kirim event sukses kembali ke browser
            $this->dispatch('user-deleted', message: 'User has been successfully deleted!');
        } else {
            // Kirim event gagal jika user tidak ditemukan
            $this->dispatch('delete-failed', message: 'Error: User not found.');
        }
    }
}
