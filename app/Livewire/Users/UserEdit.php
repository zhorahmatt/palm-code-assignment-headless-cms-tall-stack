<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;

class UserEdit extends Component
{
    public User $user;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|string|email|max:255')]
    public $email = '';

    #[Validate('nullable|string|min:8|confirmed')]
    public $password = '';

    #[Validate('nullable|string|min:8')]
    public $password_confirmation = '';

    #[Validate('boolean')]
    public $is_active = true;

    #[Validate('array')]
    public $selectedRoles = [];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_active = $user->is_active;
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string|min:8',
            'is_active' => 'boolean',
            'selectedRoles' => 'array',
            'selectedRoles.*' => 'exists:roles,id'
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
                'is_active' => $this->is_active,
            ];

            // Only update password if provided
            if (!empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }

            $this->user->update($updateData);

            // Update roles (only if current user is superadmin or editing non-superadmin)
            if (auth()->user()->isSuperAdmin() || !$this->user->isSuperAdmin()) {
                $this->user->roles()->sync($this->selectedRoles);
            }

            session()->flash('message', 'User updated successfully.');
            return $this->redirect(route('users.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update user. Please try again.');
        }
    }

    public function delete()
    {
        try {
            // Prevent deletion of current user
            if ($this->user->id === auth()->id()) {
                session()->flash('error', 'You cannot delete your own account.');
                return;
            }

            // Prevent deletion of superadmin users (except by other superadmins)
            if ($this->user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
                session()->flash('error', 'You cannot delete a superadmin user.');
                return;
            }

            $this->user->delete();
            session()->flash('message', 'User deleted successfully.');
            return $this->redirect(route('users.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete user. Please try again.');
        }
    }

    public function render()
    {
        $roles = Role::where('is_active', true)->orderBy('display_name')->get();

        return view('livewire.users.user-edit', [
            'roles' => $roles,
        ]);
    }
}
