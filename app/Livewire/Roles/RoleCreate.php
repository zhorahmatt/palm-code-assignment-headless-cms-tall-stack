<?php

namespace App\Livewire\Roles;

use App\Models\Role;
use App\Models\Permission;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;

class RoleCreate extends Component
{
    #[Validate('required|string|max:255|unique:roles,name')]
    public $name = '';
    
    #[Validate('required|string|max:255')]
    public $display_name = '';
    
    #[Validate('nullable|string')]
    public $description = '';
    
    #[Validate('boolean')]
    public $is_active = true;
    
    public $selectedPermissions = [];

    public function updatedName()
    {
        $this->display_name = Str::title(str_replace(['_', '-'], ' ', $this->name));
    }

    public function save()
    {
        $this->validate();

        try {
            $role = Role::create([
                'name' => $this->name,
                'display_name' => $this->display_name,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);
            
            // Attach selected permissions
            if (!empty($this->selectedPermissions)) {
                $role->permissions()->attach($this->selectedPermissions);
            }

            session()->flash('message', 'Role created successfully.');
            return $this->redirect(route('roles.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create role. Please try again.');
        }
    }

    public function render()
    {
        $permissions = Permission::where('is_active', true)
            ->orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group');
            
        return view('livewire.roles.role-create', [
            'permissions' => $permissions,
        ]);
    }
}