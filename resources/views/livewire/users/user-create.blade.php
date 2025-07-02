<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Create New User</h2>
                        <a href="{{ route('users.index') }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Users
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
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Full Name *
                                    </label>
                                    <input type="text"
                                           id="name"
                                           wire:model="name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                           placeholder="Enter user's full name...">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Address *
                                    </label>
                                    <input type="email"
                                           id="email"
                                           wire:model="email"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                                           placeholder="user@example.com">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Password *
                                    </label>
                                    <input type="password"
                                           id="password"
                                           wire:model="password"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                                           placeholder="Enter password (min. 8 characters)">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Password Confirmation -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                        Confirm Password *
                                    </label>
                                    <input type="password"
                                           id="password_confirmation"
                                           wire:model="password_confirmation"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password_confirmation') border-red-500 @enderror"
                                           placeholder="Confirm password">
                                    @error('password_confirmation')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Roles Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Assign Roles
                                    </label>
                                    <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-300 rounded-md p-3">
                                        @forelse($roles as $role)
                                            <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded">
                                                <input type="checkbox"
                                                       wire:model="selectedRoles"
                                                       value="{{ $role->id }}"
                                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-gray-900">{{ $role->display_name }}</div>
                                                    @if($role->description)
                                                        <div class="text-xs text-gray-500">{{ $role->description }}</div>
                                                    @endif
                                                </div>
                                            </label>
                                        @empty
                                            <p class="text-sm text-gray-500 italic">No active roles available</p>
                                        @endforelse
                                    </div>
                                    @error('selectedRoles')
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
                                            <span wire:loading.remove>Create User</span>
                                            <span wire:loading>Creating...</span>
                                        </button>

                                        <a href="{{ route('users.index') }}"
                                           class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center block">
                                            Cancel
                                        </a>
                                    </div>
                                </div>

                                <!-- User Settings -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">User Settings</h3>

                                    <div class="space-y-3">
                                        <!-- Active Status -->
                                        <label class="flex items-center space-x-3">
                                            <input type="checkbox"
                                                   wire:model="is_active"
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <span class="text-sm font-medium text-gray-700">Active User</span>
                                        </label>
                                        <p class="text-xs text-gray-500">Inactive users cannot log in</p>
                                    </div>
                                </div>

                                <!-- Help -->
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-blue-900 mb-2">Tips</h3>
                                    <ul class="text-sm text-blue-800 space-y-1">
                                        <li>• Use a strong password (min. 8 characters)</li>
                                        <li>• Assign appropriate roles for access control</li>
                                        <li>• Email will be auto-verified for admin-created users</li>
                                        <li>• Users can change their password after first login</li>
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
