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

    // Modal state properties
    public $showDeleteModal = false;
    public $userToDelete = null;
    public $userToDeleteName = '';
    
    // Status change modal properties
    public $showStatusModal = false;
    public $userToChangeStatus = null;
    public $userToChangeStatusName = '';
    public $newStatus = '';

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

    public function confirmDelete($userId)
    {
        $user = User::find($userId);
        if ($user) {
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

            $this->userToDelete = $userId;
            $this->userToDeleteName = $user->name;
            $this->showDeleteModal = true;
        }
    }

    public function deleteUser()
    {
        try {
            $user = User::findOrFail($this->userToDelete);

            // Double-check permissions
            if ($user->id === auth()->id()) {
                session()->flash('error', 'You cannot delete your own account.');
                $this->closeDeleteModal();
                return;
            }

            if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
                session()->flash('error', 'You cannot delete a superadmin user.');
                $this->closeDeleteModal();
                return;
            }

            $userName = $user->name;
            $user->delete();
            session()->flash('message', "User '{$userName}' has been deleted successfully.");
            $this->closeDeleteModal();

            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete user. Please try again.');
            $this->closeDeleteModal();
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->userToDelete = null;
        $this->userToDeleteName = '';
    }

    public function confirmStatusChange($userId)
    {
        $user = User::find($userId);
        if ($user) {
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

            $this->userToChangeStatus = $userId;
            $this->userToChangeStatusName = $user->name;
            $this->newStatus = $user->is_active ? 'inactive' : 'active';
            $this->showStatusModal = true;
        }
    }

    public function changeUserStatus()
    {
        try {
            $user = User::findOrFail($this->userToChangeStatus);

            // Double-check permissions
            if ($user->id === auth()->id()) {
                session()->flash('error', 'You cannot deactivate your own account.');
                $this->closeStatusModal();
                return;
            }

            if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
                session()->flash('error', 'You cannot deactivate a superadmin user.');
                $this->closeStatusModal();
                return;
            }

            $user->update(['is_active' => !$user->is_active]);
            
            $action = $user->is_active ? 'activated' : 'deactivated';
            session()->flash('message', "User '{$user->name}' has been {$action} successfully.");
            
            $this->closeStatusModal();
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update user status. Please try again.');
            $this->closeStatusModal();
        }
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->userToChangeStatus = null;
        $this->userToChangeStatusName = '';
        $this->newStatus = '';
    }

    public function toggleStatus($userId)
    {
        // This method is now replaced by confirmStatusChange
        $this->confirmStatusChange($userId);
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
