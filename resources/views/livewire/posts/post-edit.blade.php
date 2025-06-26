<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Edit Post</h2>
                            <p class="text-sm text-gray-600 mt-1">Last updated: {{ $post->updated_at->format('M d, Y \\a\\t g:i A') }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('posts.index') }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to Posts
                            </a>
                            @if($post->status === 'published')
                                <a href="{{ url('/api/v1/posts/' . $post->slug) }}"
                                   target="_blank"
                                   class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    View Live
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Form -->
                    <form wire:submit="update">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Main Content -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Title -->
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                        Title *
                                    </label>
                                    <input type="text"
                                           id="title"
                                           wire:model.live="title"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                                           placeholder="Enter post title...">
                                    @error('title')
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
                                           placeholder="post-slug">
                                    @error('slug')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">URL-friendly version of the title</p>
                                </div>

                                <!-- Excerpt -->
                                <div>
                                    <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                                        Excerpt
                                    </label>
                                    <textarea id="excerpt"
                                              wire:model="excerpt"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('excerpt') border-red-500 @enderror"
                                              placeholder="Brief description of the post..."></textarea>
                                    @error('excerpt')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Content -->
                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                        Content *
                                    </label>
                                    <textarea id="content"
                                              wire:model="content"
                                              rows="15"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('content') border-red-500 @enderror"
                                              placeholder="Write your post content here..."></textarea>
                                    @error('content')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- SEO Section -->
                                <div class="border-t pt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h3>

                                    <!-- Meta Title -->
                                    <div class="mb-4">
                                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                                            Meta Title
                                        </label>
                                        <input type="text"
                                               id="meta_title"
                                               wire:model="meta_title"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('meta_title') border-red-500 @enderror"
                                               placeholder="SEO title for search engines...">
                                        @error('meta_title')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-sm text-gray-500">Recommended: 50-60 characters</p>
                                    </div>

                                    <!-- Meta Description -->
                                    <div>
                                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                            Meta Description
                                        </label>
                                        <textarea id="meta_description"
                                                  wire:model="meta_description"
                                                  rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('meta_description') border-red-500 @enderror"
                                                  placeholder="SEO description for search engines..."></textarea>
                                        @error('meta_description')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-sm text-gray-500">Recommended: 150-160 characters</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar -->
                            <div class="space-y-6">
                                <!-- Publish Settings -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Publish Settings</h3>

                                    <!-- Status -->
                                    <div class="mb-4">
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                            Status
                                        </label>
                                        <select id="status"
                                                wire:model="status"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="draft">Draft</option>
                                            <option value="published">Published</option>
                                        </select>
                                    </div>

                                    <!-- Published At -->
                                    <div class="mb-4">
                                        <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                                            Publish Date
                                        </label>
                                        <input type="datetime-local"
                                               id="published_at"
                                               wire:model="published_at"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <p class="mt-1 text-sm text-gray-500">Leave empty for immediate publish</p>
                                    </div>

                                    <!-- Save Buttons -->
                                    <div class="space-y-2">
                                        <button type="submit"
                                                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            <span wire:loading.remove>Update Post</span>
                                            <span wire:loading>Updating...</span>
                                        </button>

                                        <button type="button"
                                                wire:click="saveDraft"
                                                class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            <span wire:loading.remove wire:target="saveDraft">Save as Draft</span>
                                            <span wire:loading wire:target="saveDraft">Saving...</span>
                                        </button>

                                        <button type="button"
                                                wire:click="deletePost"
                                                wire:confirm="Are you sure you want to delete this post? This action cannot be undone."
                                                class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            <span wire:loading.remove wire:target="deletePost">Delete Post</span>
                                            <span wire:loading wire:target="deletePost">Deleting...</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Featured Image -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Featured Image</h3>

                                    <!-- Current Image -->
                                    @if($post->image && !$featured_image)
                                        <div class="mb-4">
                                            <img src="{{ Storage::url($post->image) }}"
                                                 alt="Current featured image"
                                                 class="w-full h-32 object-cover rounded-lg">
                                            <button type="button"
                                                    wire:click="removeCurrentImage"
                                                    class="mt-2 text-sm text-red-600 hover:text-red-800">
                                                Remove current image
                                            </button>
                                        </div>
                                    @endif

                                    <div class="mb-4">
                                        <input type="file"
                                               wire:model="featured_image"
                                               accept="image/*"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @error('featured_image')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- New Image Preview -->
                                    @if ($featured_image)
                                        <div class="mt-4">
                                            <p class="text-sm text-gray-600 mb-2">New image preview:</p>
                                            <img src="{{ $featured_image->temporaryUrl() }}"
                                                 alt="Preview"
                                                 class="w-full h-32 object-cover rounded-lg">
                                        </div>
                                    @endif
                                </div>

                                <!-- Categories -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Categories</h3>

                                    <div class="space-y-2 max-h-40 overflow-y-auto">
                                        @foreach($categories as $category)
                                            <label class="flex items-center">
                                                <input type="checkbox"
                                                       wire:model="selectedCategories"
                                                       value="{{ $category->id }}"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    @if($categories->isEmpty())
                                        <p class="text-sm text-gray-500">No categories available.
                                            <a href="{{ route('categories.create') }}" class="text-blue-600 hover:text-blue-800">Create one</a>
                                        </p>
                                    @endif
                                </div>

                                <!-- Post Statistics -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Post Statistics</h3>

                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex justify-between">
                                            <span>Created:</span>
                                            <span>{{ $post->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Last Updated:</span>
                                            <span>{{ $post->updated_at->format('M d, Y') }}</span>
                                        </div>
                                        @if($post->published_at)
                                            <div class="flex justify-between">
                                                <span>Published:</span>
                                                <span>{{ $post->published_at->format('M d, Y') }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between">
                                            <span>Word Count:</span>
                                            <span>{{ str_word_count(strip_tags($post->content)) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
             x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 3000)">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50"
             x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 3000)">
            {{ session('error') }}
        </div>
    @endif
</div>
