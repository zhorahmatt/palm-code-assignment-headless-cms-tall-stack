<?php

namespace App\Livewire\Permissions;

use App\Models\Permission;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;

class PermissionCreate extends Component
{
    #[Validate('required|string|max:255|unique:permissions,name')]
    public $name = '';
    
    #[Validate('required|string|max:255')]
    public $display_name = '';
    
    #[Validate('nullable|string')]
    public $description = '';
    
    #[Validate('required|string|max:255')]
    public $group = '';
    
    #[Validate('boolean')]
    public $is_active = true;

    public function updatedName()
    {
        $this->display_name = Str::title(str_replace(['_', '-', '.'], ' ', $this->name));
    }

    public function save()
    {
        $this->validate();

        try {
            Permission::create([
                'name' => $this->name,
                'display_name' => $this->display_name,
                'description' => $this->description,
                'group' => $this->group,
                'is_active' => $this->is_active,
            ]);

            session()->flash('message', 'Permission created successfully.');
            return $this->redirect(route('permissions.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create permission. Please try again.');
        }
    }

    public function render()
    {
        $existingGroups = Permission::distinct('group')
            ->whereNotNull('group')
            ->orderBy('group')
            ->pluck('group');
            
        return view('livewire.permissions.permission-create', [
            'existingGroups' => $existingGroups,
        ]);
    }
}