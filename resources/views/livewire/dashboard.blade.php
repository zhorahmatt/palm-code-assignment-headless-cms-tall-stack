<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Header Section --}}
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
                        <p class="mt-2 text-sm text-gray-700">Welcome to your content management system</p>
                    </div>

                    {{-- Why we use a grid layout: Responsive design that works on all devices --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        {{-- Posts Statistics Card --}}
                        <div class="bg-gray-50 rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Posts</h3>
                                    <p class="text-2xl font-bold text-blue-600">{{ $postsCount }}</p>
                                    <p class="text-sm text-gray-500">{{ $publishedPostsCount }} published, {{ $draftPostsCount }} drafts</p>
                                </div>
                            </div>
                        </div>

                        {{-- Pages Statistics Card --}}
                        <div class="bg-gray-50 rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Pages</h3>
                                    <p class="text-2xl font-bold text-green-600">{{ $pagesCount }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Categories Statistics Card --}}
                        <div class="bg-gray-50 rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Categories</h3>
                                    <p class="text-2xl font-bold text-purple-600">{{ $categoriesCount }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Actions Card --}}
                        <div class="bg-gray-50 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-2">
                                <a href="{{ route('posts.create') }}" class="block w-full text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                    New Post
                                </a>
                                <a href="{{ route('pages.create') }}" class="block w-full text-center bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
                                    New Page
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Posts Section --}}
                    <div class="bg-gray-50 rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Posts</h3>
                        </div>
                        <div class="p-6">
                            {{-- This will be populated with recent posts data --}}
                            <p class="text-gray-500">Recent posts will be displayed here...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
