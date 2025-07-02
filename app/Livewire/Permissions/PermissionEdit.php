<?php

namespace App\Livewire\Permissions;

use App\Models\Permission;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;

class PermissionEdit extends Component
{
    public Permission $permission;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|string|max:255')]
    public $display_name = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|string|max:255')]
    public $group = '';

    #[Validate('boolean')]
    public $is_active = true;

    public function mount(Permission $permission)
    {
        $this->permission = $permission;
        $this->name = $permission->name;
        $this->display_name = $permission->display_name;
        $this->description = $permission->description;
        $this->group = $permission->group;
        $this->is_active = $permission->is_active;
    }

    public function updatedName()
    {
        $this->display_name = Str::title(str_replace(['_', '-', '.'], ' ', $this->name));
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:permissions,name,' . $this->permission->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $this->permission->update([
                'name' => $this->name,
                'display_name' => $this->display_name,
                'description' => $this->description,
                'group' => $this->group,
                'is_active' => $this->is_active,
            ]);

            session()->flash('message', 'Permission updated successfully.');
            return $this->redirect(route('permissions.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update permission. Please try again.');
        }
    }

    public function delete()
    {
        try {
            // Check if permission is assigned to roles
            if ($this->permission->roles()->count() > 0) {
                session()->flash('error', 'Cannot delete permission assigned to roles.');
                return;
            }

            $this->permission->delete();
            session()->flash('message', 'Permission deleted successfully.');
            return $this->redirect(route('permissions.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete permission. Please try again.');
        }
    }

    public function render()
    {
        $existingGroups = Permission::distinct('group')
            ->whereNotNull('group')
            ->orderBy('group')
            ->pluck('group');

        return view('livewire.permissions.permission-edit', [
            'existingGroups' => $existingGroups,
        ]);
    }
}
