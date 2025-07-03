<div>
    <!-- Flash Notifications Component -->
    <x-flash-notifications />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Roles Management</h2>
                        <a href="{{ route('roles.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create New Role
                        </a>
                    </div>

                    <!-- Search and Filters -->
                    <div class="mb-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <input type="text"
                                    wire:model.live.debounce.300ms="search"
                                    placeholder="Search roles by name, display name, or description..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Roles Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('display_name')">
                                        Role Name
                                        @if($sortField === 'display_name')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('name')">
                                        System Name
                                        @if($sortField === 'name')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Users
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Permissions
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('is_active')">
                                        Status
                                        @if($sortField === 'is_active')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('created_at')">
                                        Created
                                        @if($sortField === 'created_at')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($roles as $role)
                                    <tr class="hover:bg-gray-50" wire:key="role-{{ $role->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $role->display_name }}
                                                        @if($role->name === 'superadmin')
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                Super Admin
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">
                                                {{ $role->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $role->description }}">
                                                {{ $role->description ?: 'No description' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $role->permissions_count }} {{ Str::plural('permission', $role->permissions_count) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap" wire:key="status-{{ $role->id }}">
                                            <button wire:click="confirmStatusChange({{ $role->id }})"
                                                    @if($role->name === 'superadmin') disabled @endif
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors duration-200
                                                           @if($role->is_active)
                                                               bg-green-100 text-green-800 hover:bg-green-200
                                                           @else
                                                               bg-red-100 text-red-800 hover:bg-red-200
                                                           @endif
                                                           @if($role->name === 'superadmin') opacity-50 cursor-not-allowed @else cursor-pointer @endif">
                                                {{ $role->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $role->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('roles.edit', $role) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                                    Edit
                                                </a>
                                                @if($role->name !== 'superadmin' && $role->users_count === 0)
                                                    <button wire:click="confirmDelete({{ $role->id }})"
                                                            class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                                        Delete
                                                    </button>
                                                @else
                                                    <span class="text-gray-400 cursor-not-allowed" title="@if($role->name === 'superadmin') Cannot delete superadmin role @else Role has assigned users @endif">
                                                        Delete
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            @if($search)
                                                No roles found matching "{{ $search }}".
                                            @else
                                                No roles found. <a href="{{ route('roles.create') }}" class="text-blue-600 hover:text-blue-900">Create the first role</a>.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($roles->hasPages())
                        <div class="mt-6">
                            {{ $roles->links() }}
                        </div>
                    @endif

                    <!-- Summary -->
                    <div class="mt-6 text-sm text-gray-600">
                        Showing {{ $roles->firstItem() ?? 0 }} to {{ $roles->lastItem() ?? 0 }} of {{ $roles->total() }} roles
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal Component -->
    <x-delete-modal 
        :show="$showDeleteModal"
        title="Delete Role"
        message="Are you sure you want to delete the role"
        :itemName="$roleToDeleteName"
        deleteMethod="deleteRole"
        closeMethod="closeDeleteModal" />

    <!-- Status Modal Component -->
    <x-status-modal 
        :show="$showStatusModal"
        title="Change Role Status"
        message="Are you sure you want to change the status of role"
        :itemName="$roleToChangeStatusName"
        :newStatus="$newStatus"
        statusMethod="changeRoleStatus"
        closeMethod="closeStatusModal" />
</div>
