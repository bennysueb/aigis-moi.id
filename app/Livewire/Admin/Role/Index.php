<?php

namespace App\Livewire\Admin\Role;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public $role_id;
    public $name;
    public $assignedPermissions = [];
    public $allPermissions;
    public $showModal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public $isEditMode = false;

    public function mount()
    {
        $this->allPermissions = Permission::all();
    }

    public function render()
    {
        $roles = Role::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->with('permissions')
            ->paginate(15);

        return view('livewire.admin.role.index', ['roles' => $roles])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->role_id = $role->id;
        $this->name = $role->name;
        $this->assignedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate(['name' => 'required|string|unique:roles,name,' . $this->role_id]);

        if ($this->isEditMode) {
            $role = Role::findOrFail($this->role_id);
            $role->update(['name' => $this->name]);
        } else {
            $role = Role::create(['name' => $this->name]);
        }

        $role->syncPermissions($this->assignedPermissions);

        $this->closeModal();
        session()->flash('message', 'Role successfully saved.');
    }

    public function delete($id)
    {
        Role::findOrFail($id)->delete();
        session()->flash('message', 'Role successfully deleted.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->role_id = null;
        $this->name = '';
        $this->assignedPermissions = [];
    }
}
