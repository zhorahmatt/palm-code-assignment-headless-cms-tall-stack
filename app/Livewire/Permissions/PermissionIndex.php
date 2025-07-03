<?php

namespace App\Livewire\Permissions;

use App\Models\Permission;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class PermissionIndex extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $groupFilter = '';

    public $sortField = 'group';
    public $sortDirection = 'asc';

    // Add these modal properties
    public $showDeleteModal = false;
    public $permissionToDelete = null;
    public $permissionToDeleteName = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'groupFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingGroupFilter()
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

    // Add these modal methods
    // Add this new method for confirming delete
    public function confirmDelete($permissionId)
    {
        $permission = Permission::find($permissionId);


        if (!$permission) {
            session()->flash('error', 'Permission not found.');
            return;
        }

        $this->permissionToDelete = $permissionId;
        $this->permissionToDeleteName = $permission->display_name;
        $this->showDeleteModal = true;
    }

    // Rename the existing delete method to deletePermission
    public function deletePermission()
    {
        try {
            if ($this->permissionToDelete) {
                $permission = Permission::findOrFail($this->permissionToDelete);

                // Check if permission is assigned to roles
                if ($permission->roles()->count() > 0) {
                    session()->flash('error', 'Cannot delete permission assigned to roles.');
                    $this->closeDeleteModal();
                    return;
                }

                $permission->delete();
                session()->flash('message', 'Permission deleted successfully.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete permission. Please try again.');
        } finally {
            $this->closeDeleteModal();
        }
    }

    // Add this method to close the modal
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->permissionToDelete = null;
        $this->permissionToDeleteName = '';
    }

    // Remove or comment out the old delete method
    // public function delete($permissionId) { ... }

    public function toggleStatus($permissionId)
    {
        try {
            $permission = Permission::findOrFail($permissionId);
            $permission->update(['is_active' => !$permission->is_active]);
            session()->flash('message', 'Permission status updated successfully.');

            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update permission status.');
        }
    }

    public function render()
    {
        $query = Permission::withCount('roles')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('display_name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->groupFilter, function ($query) {
                $query->where('group', $this->groupFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $permissions = $query->paginate(15);

        $groups = Permission::distinct('group')
            ->whereNotNull('group')
            ->orderBy('group')
            ->pluck('group');

        return view('livewire.permissions.permission-index', [
            'permissions' => $permissions,
            'groups' => $groups,
        ]);
    }
}
