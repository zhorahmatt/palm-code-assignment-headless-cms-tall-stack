<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class UserIndex extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $roleFilter = '';

    #[Url]
    public $statusFilter = '';

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
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

    public function deleteUser($userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Prevent deletion of current user
            if ($user->id === auth()->id()) {
                session()->flash('error', 'You cannot delete your own account.');
                return;
            }

            // Prevent deletion of superadmin users (except by other superadmins)
            if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
                session()->flash('error', 'You cannot delete a superadmin user.');
                return;
            }

            $user->delete();
            session()->flash('message', 'User deleted successfully.');

            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete user. Please try again.');
        }
    }

    public function toggleStatus($userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Prevent deactivating current user
            if ($user->id === auth()->id()) {
                session()->flash('error', 'You cannot deactivate your own account.');
                return;
            }

            // Prevent deactivating superadmin users (except by other superadmins)
            if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
                session()->flash('error', 'You cannot deactivate a superadmin user.');
                return;
            }

            $user->update(['is_active' => !$user->is_active]);
            session()->flash('message', 'User status updated successfully.');

            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update user status.');
        }
    }

    public function render()
    {
        $query = User::with('roles')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->roleFilter, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('roles.id', $this->roleFilter);
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $users = $query->paginate(10);

        $roles = Role::where('is_active', true)->orderBy('display_name')->get();

        return view('livewire.users.user-index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
