<?php

namespace App\Livewire\Roles;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class RoleIndex extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';
    
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Modal state properties
    public $showDeleteModal = false;
    public $roleToDelete = null;
    public $roleToDeleteName = '';
    
    // Status change modal properties
    public $showStatusModal = false;
    public $roleToChangeStatus = null;
    public $roleToChangeStatusName = '';
    public $newStatus = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function confirmDelete($roleId)
    {
        $role = Role::find($roleId);
        if ($role) {
            // Prevent deletion of superadmin role
            if ($role->name === 'superadmin') {
                session()->flash('error', 'Cannot delete the superadmin role.');
                return;
            }
            
            // Check if role has users
            if ($role->users()->count() > 0) {
                session()->flash('error', 'Cannot delete role with assigned users.');
                return;
            }

            $this->roleToDelete = $roleId;
            $this->roleToDeleteName = $role->display_name;
            $this->showDeleteModal = true;
        }
    }

    public function deleteRole()
    {
        try {
            $role = Role::findOrFail($this->roleToDelete);
            
            // Double-check permissions
            if ($role->name === 'superadmin') {
                session()->flash('error', 'Cannot delete the superadmin role.');
                $this->closeDeleteModal();
                return;
            }
            
            // Check if role has users
            if ($role->users()->count() > 0) {
                session()->flash('error', 'Cannot delete role with assigned users.');
                $this->closeDeleteModal();
                return;
            }
            
            $roleName = $role->display_name;
            $role->delete();
            session()->flash('message', "Role '{$roleName}' has been deleted successfully.");
            $this->closeDeleteModal();

            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete role. Please try again.');
            $this->closeDeleteModal();
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->roleToDelete = null;
        $this->roleToDeleteName = '';
    }

    public function confirmStatusChange($roleId)
    {
        $role = Role::find($roleId);
        if ($role) {
            // Prevent deactivating superadmin role
            if ($role->name === 'superadmin' && $role->is_active) {
                session()->flash('error', 'Cannot deactivate the superadmin role.');
                return;
            }

            $this->roleToChangeStatus = $roleId;
            $this->roleToChangeStatusName = $role->display_name;
            $this->newStatus = $role->is_active ? 'inactive' : 'active';
            $this->showStatusModal = true;
        }
    }

    public function changeRoleStatus()
    {
        try {
            $role = Role::findOrFail($this->roleToChangeStatus);

            // Double-check permissions
            if ($role->name === 'superadmin' && $role->is_active) {
                session()->flash('error', 'Cannot deactivate the superadmin role.');
                $this->closeStatusModal();
                return;
            }

            $role->update(['is_active' => !$role->is_active]);
            
            $action = $role->is_active ? 'activated' : 'deactivated';
            session()->flash('message', "Role '{$role->display_name}' has been {$action} successfully.");
            
            $this->closeStatusModal();
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update role status. Please try again.');
            $this->closeStatusModal();
        }
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->roleToChangeStatus = null;
        $this->roleToChangeStatusName = '';
        $this->newStatus = '';
    }

    public function delete($roleId)
    {
        // This method is now replaced by confirmDelete
        $this->confirmDelete($roleId);
    }

    public function toggleStatus($roleId)
    {
        // This method is now replaced by confirmStatusChange
        $this->confirmStatusChange($roleId);
    }

    public function render()
    {
        $query = Role::withCount(['users', 'permissions'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('display_name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $roles = $query->paginate(10);

        return view('livewire.roles.role-index', [
            'roles' => $roles,
        ]);
    }
}