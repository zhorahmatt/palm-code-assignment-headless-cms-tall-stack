<?php

namespace App\Livewire\Roles;

use App\Models\Role;
use App\Models\Permission;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;

class RoleEdit extends Component
{
    public Role $role;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|string|max:255')]
    public $display_name = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('boolean')]
    public $is_active = true;

    public $selectedPermissions = [];

    public function mount(Role $role)
    {
        $this->role = $role;
        $this->name = $role->name;
        $this->display_name = $role->display_name;
        $this->description = $role->description;
        $this->is_active = $role->is_active;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
    }

    public function updatedName()
    {
        if ($this->role->name !== 'superadmin') {
            $this->display_name = Str::title(str_replace(['_', '-'], ' ', $this->name));
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $this->role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        // Prevent editing critical fields of superadmin role
        if ($this->role->name === 'superadmin') {
            $this->validate([
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $this->role->update([
                'display_name' => $this->display_name,
                'description' => $this->description,
            ]);
        } else {
            $this->validate();

            $this->role->update([
                'name' => $this->name,
                'display_name' => $this->display_name,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);
        }

        // Update permissions
        $this->role->permissions()->sync($this->selectedPermissions);

        session()->flash('message', 'Role updated successfully.');
        return $this->redirect(route('roles.index'), navigate: true);
    }

    public function delete()
    {
        try {
            // Prevent deletion of superadmin role
            if ($this->role->name === 'superadmin') {
                session()->flash('error', 'Cannot delete the superadmin role.');
                return;
            }

            // Check if role has users
            if ($this->role->users()->count() > 0) {
                session()->flash('error', 'Cannot delete role with assigned users.');
                return;
            }

            $this->role->delete();
            session()->flash('message', 'Role deleted successfully.');
            return $this->redirect(route('roles.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete role. Please try again.');
        }
    }

    public function render()
    {
        $permissions = Permission::where('is_active', true)
            ->orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group');

        return view('livewire.roles.role-edit', [
            'permissions' => $permissions,
        ]);
    }
}
