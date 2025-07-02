<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;

class UserCreate extends Component
{
    #[Validate('required|string|max:255')]
    public $name = '';
    
    #[Validate('required|string|email|max:255|unique:users,email')]
    public $email = '';
    
    #[Validate('required|string|min:8|confirmed')]
    public $password = '';
    
    #[Validate('required|string|min:8')]
    public $password_confirmation = '';
    
    #[Validate('boolean')]
    public $is_active = true;
    
    #[Validate('array')]
    public $selectedRoles = [];
    
    #[Validate('boolean')]
    public $send_welcome_email = true;

    public function save()
    {
        $this->validate([
            'selectedRoles.*' => 'exists:roles,id'
        ]);

        try {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'is_active' => $this->is_active,
                'email_verified_at' => now(), // Auto-verify admin created users
            ]);
            
            // Attach selected roles
            if (!empty($this->selectedRoles)) {
                $user->roles()->attach($this->selectedRoles);
            }
            
            // TODO: Send welcome email if requested
            // if ($this->send_welcome_email) {
            //     // Send welcome email logic
            // }

            session()->flash('message', 'User created successfully.');
            return $this->redirect(route('users.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create user. Please try again.');
        }
    }

    public function render()
    {
        $roles = Role::where('is_active', true)->orderBy('display_name')->get();
        
        return view('livewire.users.user-create', [
            'roles' => $roles,
        ]);
    }
}
