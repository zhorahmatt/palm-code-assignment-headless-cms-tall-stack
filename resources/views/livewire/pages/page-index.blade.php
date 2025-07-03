<div>
    <!-- Flash Notifications Component -->
    <x-flash-notifications />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Pages Management</h2>
                        <a href="{{ route('pages.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create New Page
                        </a>
                    </div>

                    <!-- Filters -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <input type="text"
                                wire:model.live="search"
                                placeholder="Search pages..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <select wire:model.live="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Status</option>
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>

                        <!-- Empty placeholder to maintain 4-column layout -->
                        <div></div>

                        <!-- Sort -->
                        <div>
                            <select wire:model.live="sortField"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="created_at">Created Date</option>
                                <option value="title">Title</option>
                                <option value="status">Status</option>
                                <option value="updated_at">Updated Date</option>
                            </select>
                        </div>
                    </div>

                    <!-- Pages Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('title')">
                                        Title
                                        @if($sortField === 'title')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Slug
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('status')">
                                        Status
                                        @if($sortField === 'status')
                                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                        @endif
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        wire:click="sortBy('updated_at')">
                                        Updated
                                        @if($sortField === 'updated_at')
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
                                @forelse($pages as $page)
                                    <tr class="hover:bg-gray-50" wire:key="page-{{ $page->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full bg-purple-300 mr-3 flex items-center justify-center">
                                                    <span class="text-purple-600 text-sm">📄</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $page->title }}</div>
                                                    <div class="text-sm text-gray-500">{{ Str::limit(strip_tags($page->body), 50) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $page->slug }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap" wire:key="status-{{ $page->id }}">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors duration-200
                                                {{ $page->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($page->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $page->updated_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $page->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('pages.edit', $page) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">Edit</a>

                                                <button wire:click="confirmStatusChange({{ $page->id }})"
                                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                                    {{ $page->status === 'published' ? 'Unpublish' : 'Publish' }}
                                                </button>

                                                <button wire:click="confirmDelete({{ $page->id }})"
                                                        class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No pages found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $pages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal Component -->
    <x-delete-modal
        :show="$showDeleteModal"
        title="Delete Page"
        message="Are you sure you want to delete the page"
        :itemName="$pageToDeleteTitle"
        deleteMethod="deletePage"
        closeMethod="closeDeleteModal" />

    <!-- Status Modal Component -->
    <x-status-modal
        :show="$showStatusModal"
        title="Change Page Status"
        message="Are you sure you want to change the status of post"
        :itemName="$pageToChangeStatusTitle"
        :newStatus="$newStatus"
        statusMethod="changePageStatus"
        closeMethod="closeStatusModal" />
</div>
