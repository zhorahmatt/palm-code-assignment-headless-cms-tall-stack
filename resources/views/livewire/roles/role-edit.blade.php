<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Edit Role</h2>
                            <p class="text-sm text-gray-600 mt-1">Editing: {{ $role->display_name }}</p>
                        </div>
                        <a href="{{ route('roles.index') }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Roles
                        </a>
                    </div>

                    <!-- Flash Messages -->
                    @if (session()->has('message'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Superadmin Warning -->
                    @if($role->name === 'superadmin')
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm">
                                        <strong>Warning:</strong> This is the superadmin role. System name and status cannot be modified for security reasons.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Form -->
                    <form wire:submit="save">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Main Content -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Role Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Role Name (System) *
                                    </label>
                                    @if($role->name === 'superadmin')
                                        <input type="text"
                                               value="{{ $role->name }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-500"
                                               disabled>
                                        <p class="mt-1 text-xs text-gray-500">System name cannot be changed for the superadmin role.</p>
                                    @else
                                        <input type="text"
                                               id="name"
                                               wire:model.live="name"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                               placeholder="e.g., content_manager, editor">
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-xs text-gray-500">Use lowercase letters, numbers, and underscores only. This will be used internally.</p>
                                    @endif
                                </div>

                                <!-- Display Name -->
                                <div>
                                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Display Name *
                                    </label>
                                    <input type="text"
                                           id="display_name"
                                           wire:model="display_name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('display_name') border-red-500 @enderror"
                                           placeholder="e.g., Content Manager, Editor">
                                    @error('display_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">This is the human-readable name that will be displayed in the interface.</p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Description
                                    </label>
                                    <textarea id="description"
                                              wire:model="description"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                              placeholder="Describe what this role is for and what permissions it should have..."></textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Permissions Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Assign Permissions
                                    </label>
                                    @if($permissions->count() > 0)
                                        <div class="space-y-4 max-h-96 overflow-y-auto border border-gray-300 rounded-md p-4">
                                            @foreach($permissions as $group => $groupPermissions)
                                                <div class="border-b border-gray-200 pb-4 last:border-b-0">
                                                    <h4 class="text-sm font-semibold text-gray-800 mb-3 capitalize">{{ str_replace('_', ' ', $group) }}</h4>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                        @foreach($groupPermissions as $permission)
                                                            <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded">
                                                                <input type="checkbox"
                                                                       wire:model="selectedPermissions"
                                                                       value="{{ $permission->id }}"
                                                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                                <div class="flex-1">
                                                                    <div class="text-sm font-medium text-gray-900">{{ $permission->display_name }}</div>
                                                                    @if($permission->description)
                                                                        <div class="text-xs text-gray-500">{{ $permission->description }}</div>
                                                                    @endif
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="border border-gray-300 rounded-md p-4">
                                            <p class="text-sm text-gray-500 italic">No active permissions available</p>
                                        </div>
                                    @endif
                                    @error('selectedPermissions')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Sidebar -->
                            <div class="space-y-6">
                                <!-- Actions -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>

                                    <div class="space-y-3">
                                        <button type="submit"
                                                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline disabled:opacity-50"
                                                wire:loading.attr="disabled">
                                            <span wire:loading.remove>Update Role</span>
                                            <span wire:loading>Updating...</span>
                                        </button>

                                        <a href="{{ route('roles.index') }}"
                                           class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center block">
                                            Cancel
                                        </a>

                                        @if($role->name !== 'superadmin')
                                            <button type="button"
                                                    wire:click="delete"
                                                    wire:confirm="Are you sure you want to delete this role? This action cannot be undone."
                                                    class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                                Delete Role
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Role Settings -->
                                @if($role->name !== 'superadmin')
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Role Settings</h3>

                                        <div class="space-y-3">
                                            <!-- Active Status -->
                                            <label class="flex items-center space-x-3">
                                                <input type="checkbox"
                                                       wire:model="is_active"
                                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <span class="text-sm font-medium text-gray-700">Active Role</span>
                                            </label>
                                            <p class="text-xs text-gray-500">Inactive roles cannot be assigned to users</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Role Information -->
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-blue-900 mb-2">Role Information</h3>
                                    <div class="space-y-2 text-sm text-blue-800">
                                        <div class="flex justify-between">
                                            <span>Users with this role:</span>
                                            <span class="font-semibold">{{ $role->users()->count() }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Selected permissions:</span>
                                            <span class="font-semibold">{{ count($selectedPermissions) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Created:</span>
                                            <span class="font-semibold">{{ $role->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Status:</span>
                                            <span class="font-semibold {{ $role->is_active ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $role->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Help -->
                                <div class="bg-yellow-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-yellow-900 mb-2">Tips</h3>
                                    <ul class="text-sm text-yellow-800 space-y-1">
                                        <li>• Changes will affect all users with this role</li>
                                        <li>• Removing permissions may restrict user access</li>
                                        <li>• Consider the principle of least privilege</li>
                                        @if($role->users()->count() > 0)
                                            <li>• This role is assigned to {{ $role->users()->count() }} user(s)</li>
                                        @endif
                                        @if($role->name === 'superadmin')
                                            <li>• Superadmin role has special protections</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>