<div>
    <!-- Add Delete Modal at the end -->
    <x-delete-modal
        :show="$showDeleteModal"
        title="Delete Permission"
        message="Are you sure you want to delete the permission"
        :itemName="$permissionToDeleteName"
        deleteMethod="deletePermission"
        closeMethod="closeDeleteModal"
    />

    <!-- Flash Messages -->
    <x-flash-notifications />
{{--
    @if(config('app.debug'))
        <div class="fixed top-0 right-0 bg-red-500 text-white p-2 z-50">
            Modal State: {{ $showDeleteModal ? 'true' : 'false' }}<br>
            Permission ID: {{ $permissionToDelete ?? 'null' }}<br>
            Permission Name: {{ $permissionToDeleteName ?? 'null' }}
        </div>
    @endif --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Permissions Management</h2>
                        <a href="{{ route('permissions.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add New Permission
                        </a>
                    </div>

                    <!-- Search and Filters -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Permissions</label>
                            <input type="text"
                                id="search"
                                wire:model.live.debounce.300ms="search"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Search by name, display name, or description...">
                        </div>

                        <!-- Group Filter -->
                        <div>
                            <label for="groupFilter" class="block text-sm font-medium text-gray-700 mb-2">Filter by Group</label>
                            <select wire:model.live="groupFilter"
                                    id="groupFilter"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Groups</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group }}">{{ ucwords(str_replace('_', ' ', $group)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Permissions Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('group')">
                                        Group
                                        @if($sortField === 'group')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('display_name')">
                                        Permission
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
                                        Roles Count
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
                                @forelse($permissions as $permission)
                                    <tr class="hover:bg-gray-50">
                                        <!-- Group -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ ucwords(str_replace('_', ' ', $permission->group)) }}
                                            </span>
                                        </td>

                                        <!-- Permission Display Name -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $permission->display_name }}</div>
                                        </td>

                                        <!-- System Name -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 font-mono">{{ $permission->name }}</div>
                                        </td>

                                        <!-- Description -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $permission->description }}">
                                                {{ $permission->description ?: 'No description' }}
                                            </div>
                                        </td>

                                        <!-- Roles Count -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $permission->roles_count }} role(s)
                                            </span>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button wire:click="toggleStatus({{ $permission->id }})"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors duration-200 {{ $permission->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                                {{ $permission->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </td>

                                        <!-- Created Date -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $permission->created_at->format('M d, Y') }}
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('permissions.edit', $permission) }}"
                                                    class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                                    Edit
                                                </a>
                                                @if($permission->roles_count == 0)
                                                    <button wire:click="confirmDelete({{ $permission->id }})"
                                                            class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                                        Delete
                                                    </button>
                                                @else
                                                    <span class="text-gray-400 cursor-not-allowed" title="Cannot delete permission assigned to roles">
                                                        Delete
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900">No permissions found</h3>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    @if($search || $groupFilter)
                                                        Try adjusting your search criteria or filters.
                                                    @else
                                                        Get started by creating a new permission.
                                                    @endif
                                                </p>
                                                @if(!$search && !$groupFilter)
                                                    <div class="mt-6">
                                                        <a href="{{ route('permissions.create') }}"
                                                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                                            Add New Permission
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($permissions->hasPages())
                        <div class="mt-6">
                            {{ $permissions->links() }}
                        </div>
                    @endif

                    <!-- Summary -->
                    <div class="mt-6 flex justify-between items-center text-sm text-gray-500">
                        <div>
                            Showing {{ $permissions->firstItem() ?? 0 }} to {{ $permissions->lastItem() ?? 0 }} of {{ $permissions->total() }} permissions
                        </div>
                        @if($search || $groupFilter)
                            <div class="flex items-center space-x-2">
                                <span>Filters applied:</span>
                                @if($search)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Search: "{{ $search }}"
                                        <button wire:click="$set('search', '')" class="ml-1 text-blue-600 hover:text-blue-800">
                                            ×
                                        </button>
                                    </span>
                                @endif
                                @if($groupFilter)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Group: {{ ucwords(str_replace('_', ' ', $groupFilter)) }}
                                        <button wire:click="$set('groupFilter', '')" class="ml-1 text-green-600 hover:text-green-800">
                                            ×
                                        </button>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('console-log', (message) => {
            console.log('Livewire Debug:', message);
        });
    });
</script>
