<?php

namespace App\Http\Livewire\Administration;

use App\Models\Role;
use App\Models\Access;
use Livewire\Component;
use Livewire\WithPagination;

class RoleManagementController extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedRole = null;
    public $showModal = false;

    // Form fields
    public $roleId;
    public $role_name;
    public $description;
    public $status = 1;
    public $can_delete = 1;
    public $selectedAccess = [];

    // Modal mode
    public $modalMode = 'add'; // add or edit

    protected $listeners = ['deleteConfirmed' => 'delete'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus($value)
    {
        $this->status = $value ? 1 : 0;
    }

    public function updatedCanDelete($value)
    {
        $this->can_delete = $value ? 1 : 0;
    }

    public function rules()
    {
        $rules = [
            'role_name' => 'required|min:3|unique:roles,role_name',
            'description' => 'required|min:3',
            'status' => 'required|boolean',
            'can_delete' => 'required|boolean',
            'selectedAccess' => 'required|array|min:1',
            'selectedAccess.*' => 'exists:access,id',
        ];

        if ($this->modalMode === 'edit' && $this->roleId) {
            $rules['role_name'] = 'required|min:3|unique:roles,role_name,' . $this->roleId;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'role_name.required' => 'Role name is required',
            'role_name.min' => 'Role name must be at least 3 characters',
            'role_name.unique' => 'Role name already exists',
            'description.required' => 'Description is required',
            'description.min' => 'Description must be at least 3 characters',
            'status.required' => 'Status is required',
            'can_delete.required' => 'Can delete flag is required',
            'selectedAccess.required' => 'Please select at least 1 access',
            'selectedAccess.*.exists' => 'Invalid access selected',
        ];
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->modalMode = 'add';
        $this->showModal = true;
    }

    public function openEditModal($roleId)
    {
        $this->resetForm();
        $role = Role::with('access')->find($roleId);

        if (!$role) {
            session()->flash('error', 'Role not found');
            return;
        }

        $this->roleId = $role->id;
        $this->role_name = $role->role_name;
        $this->description = $role->description;
        $this->status = $role->status;
        $this->can_delete = $role->can_delete;
        $this->selectedAccess = $role->access->pluck('id')->toArray();

        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->modalMode === 'add') {
                $role = Role::create([
                    'role_name' => $this->role_name,
                    'description' => $this->description,
                    'status' => $this->status,
                    'can_delete' => $this->can_delete,
                ]);

                $role->access()->sync($this->selectedAccess);

                $this->dispatch('notification', ['type' => 'success', 'message' => 'Role created successfully']);
            } else {
                $role = Role::find($this->roleId);

                if (!$role) {
                    session()->flash('error', 'Role not found');
                    return;
                }

                $role->update([
                    'role_name' => $this->role_name,
                    'description' => $this->description,
                    'status' => $this->status,
                    'can_delete' => $this->can_delete,
                ]);

                $role->access()->sync($this->selectedAccess);

                $this->dispatch('notification', ['type' => 'success', 'message' => 'Role updated successfully']);
            }

            $this->closeModal();
        } catch (\Exception $e) {
            return $this->dispatch('notification', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function setDeleteId($roleId)
    {
        $this->roleId = $roleId;
    }

    public function delete()
    {
        try {
            $role = Role::find($this->roleId);

            if (!$role) {
                return $this->dispatch('notification', ['type' => 'error', 'message' => 'Role tidak ditemukan']);
            }

            if ($role->can_delete == 0) {
                return $this->dispatch('notification', ['type' => 'error', 'message' => 'Role ini tidak dapat dihapus']);
            }

            // Check if role is used by users
            if ($role->users()->count() > 0) {
                return $this->dispatch('notification', ['type' => 'error', 'message' => 'Tidak dapat menghapus role yang sedang digunakan oleh pengguna']);
            }

            // Detach all access before deleting
            $role->access()->detach();

            $role->delete();

            return $this->dispatch('notification', ['type' => 'success', 'message' => 'Role berhasil dihapus']);
        } catch (\Exception $e) {
            return $this->dispatch('notification', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->roleId = null;
        $this->role_name = '';
        $this->description = '';
        $this->status = 1;
        $this->can_delete = 1;
        $this->selectedAccess = [];
    }

    public function render()
    {
        $roles = Role::with('access')
            ->when($this->search, function($query) {
                $query->where('role_name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('role_name', 'asc')
            ->paginate(10);

        $allAccess = Access::where('status', 1)
            ->orderBy('access_name', 'asc')
            ->get();

        return view('livewire.administration.role-management', [
            'roles' => $roles,
            'allAccess' => $allAccess,
        ])->extends('layouts.master');
    }
}
