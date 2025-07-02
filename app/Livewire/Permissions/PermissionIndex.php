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

    public function delete($permissionId)
    {
        try {
            $permission = Permission::findOrFail($permissionId);
            
            // Check if permission is assigned to roles
            if ($permission->roles()->count() > 0) {
                session()->flash('error', 'Cannot delete permission assigned to roles.');
                return;
            }
            
            $permission->delete();
            session()->flash('message', 'Permission deleted successfully.');

            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete permission. Please try again.');
        }
    }

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