<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Edit Category</h2>
                            <p class="text-sm text-gray-600 mt-1">Last updated: {{ $category->updated_at->format('M d, Y \\a\\t g:i A') }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('categories.index') }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to Categories
                            </a>
                        </div>
                    </div>

                    <!-- Form -->
                    <form wire:submit="update">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Main Content -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Name *
                                    </label>
                                    <input type="text"
                                           id="name"
                                           wire:model.live="name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                           placeholder="Enter category name...">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Slug -->
                                <div>
                                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                        Slug *
                                    </label>
                                    <input type="text"
                                           id="slug"
                                           wire:model="slug"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('slug') border-red-500 @enderror"
                                           placeholder="category-slug">
                                    @error('slug')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">URL-friendly version of the name</p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Description
                                    </label>
                                    <textarea id="description"
                                              wire:model="description"
                                              rows="5"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                              placeholder="Brief description of the category..."></textarea>
                                    @error('description')
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
                                                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            Update Category
                                        </button>

                                        <a href="{{ route('categories.index') }}"
                                           class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center block">
                                            Cancel
                                        </a>
                                    </div>
                                </div>

                                <!-- Category Stats -->
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-green-900 mb-2">Category Stats</h3>
                                    <div class="text-sm text-green-800">
                                        <p><strong>Posts:</strong> {{ $category->posts()->count() }}</p>
                                        <p><strong>Created:</strong> {{ $category->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>

                                <!-- Help -->
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-blue-900 mb-2">Tips</h3>
                                    <ul class="text-sm text-blue-800 space-y-1">
                                        <li>• Choose a clear, descriptive name</li>
                                        <li>• Slug will be auto-generated from name</li>
                                        <li>• Description helps with SEO</li>
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
