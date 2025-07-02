<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Edit Permission</h2>
                        <a href="{{ route('permissions.index') }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Permissions
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

                    <!-- Form -->
                    <form wire:submit="save">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Main Content -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Permission Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Permission Name (System) *
                                    </label>
                                    <input type="text"
                                           id="name"
                                           wire:model.live="name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                           placeholder="e.g., posts.create, users.edit, categories.delete">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Use lowercase letters, numbers, dots, and underscores only. This will be used internally.</p>
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
                                           placeholder="e.g., Create Posts, Edit Users, Delete Categories">
                                    @error('display_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">This is the human-readable name that will be displayed in the interface.</p>
                                </div>

                                <!-- Group -->
                                <div>
                                    <label for="group" class="block text-sm font-medium text-gray-700 mb-2">
                                        Permission Group *
                                    </label>
                                    <div class="flex space-x-2">
                                        <input type="text"
                                               id="group"
                                               wire:model="group"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('group') border-red-500 @enderror"
                                               placeholder="e.g., posts, users, categories"
                                               list="existing-groups">
                                        <datalist id="existing-groups">
                                            @foreach($existingGroups as $existingGroup)
                                                <option value="{{ $existingGroup }}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    @error('group')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Group related permissions together. You can use an existing group or create a new one.</p>

                                    @if($existingGroups->count() > 0)
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-600 mb-1">Existing groups:</p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($existingGroups as $existingGroup)
                                                    <button type="button"
                                                            wire:click="$set('group', '{{ $existingGroup }}')"
                                                            class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded hover:bg-gray-200 transition-colors {{ $group === $existingGroup ? 'ring-2 ring-blue-500' : '' }}">
                                                        {{ $existingGroup }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
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
                                              placeholder="Describe what this permission allows users to do..."></textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Optional but recommended for clarity.</p>
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
                                            <span wire:loading.remove>Update Permission</span>
                                            <span wire:loading>Updating...</span>
                                        </button>

                                        <a href="{{ route('permissions.index') }}"
                                           class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center block">
                                            Cancel
                                        </a>

                                        <!-- Delete Button -->
                                        @if($permission->roles()->count() == 0)
                                            <button type="button"
                                                    wire:click="delete"
                                                    wire:confirm="Are you sure you want to delete this permission? This action cannot be undone."
                                                    class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                                Delete Permission
                                            </button>
                                        @else
                                            <div class="w-full bg-gray-300 text-gray-500 font-bold py-2 px-4 rounded text-center cursor-not-allowed"
                                                 title="Cannot delete permission assigned to roles">
                                                Delete Permission
                                            </div>
                                            <p class="text-xs text-gray-500 text-center">Cannot delete: assigned to {{ $permission->roles()->count() }} role(s)</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Permission Settings -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Permission Settings</h3>

                                    <div class="space-y-3">
                                        <!-- Active Status -->
                                        <label class="flex items-center space-x-3">
                                            <input type="checkbox"
                                                   wire:model="is_active"
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <span class="text-sm font-medium text-gray-700">Active Permission</span>
                                        </label>
                                        <p class="text-xs text-gray-500">Inactive permissions cannot be assigned to roles</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
