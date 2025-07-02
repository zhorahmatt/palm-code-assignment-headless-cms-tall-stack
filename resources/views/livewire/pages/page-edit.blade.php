<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Edit Page</h2>
                            <p class="text-sm text-gray-600 mt-1">Last updated: {{ $page->updated_at->format('M d, Y \\a\\t g:i A') }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('pages.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to Pages
                            </a>
                        </div>
                    </div>

                    <!-- Form -->
                    <form wire:submit="save">
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

                                <!-- Content -->
                                <div>
                                    <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
                                        Body *
                                    </label>
                                    <div wire:ignore>
                                        <textarea id="body"
                                            wire:model="body"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('body') border-red-500 @enderror">
                                        </textarea>
                                    </div>
                                    @error('body')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
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

                                    <!-- Save Buttons -->
                                    <div class="space-y-2">
                                        <button type="submit"
                                                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            <span wire:loading.remove>Update Page</span>
                                            <span wire:loading>Updating...</span>
                                        </button>

                                        <button type="button"
                                                wire:click="saveDraft"
                                                class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            <span wire:loading.remove wire:target="saveDraft">Save as Draft</span>
                                            <span wire:loading wire:target="saveDraft">Saving...</span>
                                        </button>

                                        <button type="button"
                                                wire:click="deletePage"
                                                wire:confirm="Are you sure you want to delete this page? This action cannot be undone."
                                                class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                            <span wire:loading.remove wire:target="deletePage">Delete Page</span>
                                            <span wire:loading wire:target="deletePage">Deleting...</span>
                                        </button>
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

<!-- Add this in the head section or before closing body -->
<script src="https://cdn.tiny.cloud/1/bgka8hwatxby8ooalrs2kw4mlz6jmb760o7m7a1zc1s76xqt/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Or use local installation -->
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '#body',
            height: 400,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            setup: function (editor) {
                editor.on('change', function () {
                    @this.set('body', editor.getContent());
                });
            }
        });
    });
</script>
