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

    public function delete($roleId)
    {
        try {
            $role = Role::findOrFail($roleId);
            
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
            
            $role->delete();
            session()->flash('message', 'Role deleted successfully.');

            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete role. Please try again.');
        }
    }

    public function toggleStatus($roleId)
    {
        try {
            $role = Role::findOrFail($roleId);
            
            // Prevent deactivating superadmin role
            if ($role->name === 'superadmin' && $role->is_active) {
                session()->flash('error', 'Cannot deactivate the superadmin role.');
                return;
            }
            
            $role->update(['is_active' => !$role->is_active]);
            session()->flash('message', 'Role status updated successfully.');
            
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update role status.');
        }
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