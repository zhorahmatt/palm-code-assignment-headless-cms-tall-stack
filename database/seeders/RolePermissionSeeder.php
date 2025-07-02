<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User management
            ['name' => 'users.view', 'display_name' => 'View Users', 'group' => 'users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'group' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'group' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'group' => 'users'],
            
            // Role management
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'group' => 'roles'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'group' => 'roles'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'group' => 'roles'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'group' => 'roles'],
            
            // Permission management
            ['name' => 'permissions.view', 'display_name' => 'View Permissions', 'group' => 'permissions'],
            ['name' => 'permissions.create', 'display_name' => 'Create Permissions', 'group' => 'permissions'],
            ['name' => 'permissions.edit', 'display_name' => 'Edit Permissions', 'group' => 'permissions'],
            ['name' => 'permissions.delete', 'display_name' => 'Delete Permissions', 'group' => 'permissions'],
            
            // Content management
            ['name' => 'posts.view', 'display_name' => 'View Posts', 'group' => 'content'],
            ['name' => 'posts.create', 'display_name' => 'Create Posts', 'group' => 'content'],
            ['name' => 'posts.edit', 'display_name' => 'Edit Posts', 'group' => 'content'],
            ['name' => 'posts.delete', 'display_name' => 'Delete Posts', 'group' => 'content'],
            
            ['name' => 'pages.view', 'display_name' => 'View Pages', 'group' => 'content'],
            ['name' => 'pages.create', 'display_name' => 'Create Pages', 'group' => 'content'],
            ['name' => 'pages.edit', 'display_name' => 'Edit Pages', 'group' => 'content'],
            ['name' => 'pages.delete', 'display_name' => 'Delete Pages', 'group' => 'content'],
            
            ['name' => 'categories.view', 'display_name' => 'View Categories', 'group' => 'content'],
            ['name' => 'categories.create', 'display_name' => 'Create Categories', 'group' => 'content'],
            ['name' => 'categories.edit', 'display_name' => 'Edit Categories', 'group' => 'content'],
            ['name' => 'categories.delete', 'display_name' => 'Delete Categories', 'group' => 'content'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'superadmin'],
            [
                'display_name' => 'Super Administrator',
                'description' => 'Has access to all system features and can manage users, roles, and permissions.'
            ]
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Can manage content but not users and roles.'
            ]
        );

        $editorRole = Role::firstOrCreate(
            ['name' => 'editor'],
            [
                'display_name' => 'Editor',
                'description' => 'Can create and edit content.'
            ]
        );

        // Assign all permissions to super admin
        $allPermissions = Permission::all();
        $superAdminRole->permissions()->sync($allPermissions->pluck('id'));

        // Assign content permissions to admin
        $contentPermissions = Permission::where('group', 'content')->get();
        $adminRole->permissions()->sync($contentPermissions->pluck('id'));

        // Assign limited content permissions to editor
        $editorPermissions = Permission::whereIn('name', [
            'posts.view', 'posts.create', 'posts.edit',
            'pages.view', 'pages.create', 'pages.edit',
            'categories.view'
        ])->get();
        $editorRole->permissions()->sync($editorPermissions->pluck('id'));

        // Create super admin user if it doesn't exist
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign super admin role
        $superAdmin->assignRole($superAdminRole);
    }
}