<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class PostIndex extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $status = '';

    #[Url]
    public $category = '';

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Modal state properties
    public $showDeleteModal = false;
    public $postToDelete = null;
    public $postToDeleteTitle = '';
    
    // Status change modal properties
    public $showStatusModal = false;
    public $postToChangeStatus = null;
    public $postToChangeStatusTitle = '';
    public $newStatus = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'category' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function confirmDelete($postId)
    {
        // Check permission before showing modal
        if (!Auth::user()->hasPermission('posts.delete') && !Auth::user()->isSuperAdmin()) {
            session()->flash('error', 'You do not have permission to delete posts.');
            return;
        }

        $post = Post::find($postId);
        if ($post) {
            $this->postToDelete = $postId;
            $this->postToDeleteTitle = $post->title;
            $this->showDeleteModal = true;
        }
    }

    public function deletePost()
    {
        // Double-check permission
        if (!Auth::user()->hasPermission('posts.delete') && !Auth::user()->isSuperAdmin()) {
            session()->flash('error', 'You do not have permission to delete posts.');
            $this->closeDeleteModal();
            return;
        }

        try {
            $post = Post::findOrFail($this->postToDelete);
            $post->delete();

            session()->flash('message', 'Post deleted successfully.');
            $this->closeDeleteModal();

            // Refresh the component to update the list
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete post. Please try again.');
            $this->closeDeleteModal();
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->postToDelete = null;
        $this->postToDeleteTitle = '';
    }

    public function confirmStatusChange($postId)
    {
        // Check permission before showing modal
        if (!Auth::user()->hasPermission('posts.edit') && !Auth::user()->isSuperAdmin()) {
            session()->flash('error', 'You do not have permission to edit posts.');
            return;
        }

        $post = Post::find($postId);
        if ($post) {
            $this->postToChangeStatus = $postId;
            $this->postToChangeStatusTitle = $post->title;
            $this->newStatus = $post->status === 'published' ? 'draft' : 'published';
            $this->showStatusModal = true;
        }
    }

    public function changePostStatus()
    {
        // Double-check permission
        if (!Auth::user()->hasPermission('posts.edit') && !Auth::user()->isSuperAdmin()) {
            session()->flash('error', 'You do not have permission to edit posts.');
            $this->closeStatusModal();
            return;
        }

        try {
            $post = Post::findOrFail($this->postToChangeStatus);
            $post->status = $this->newStatus;

            // Set published_at when publishing
            if ($post->status === 'published' && !$post->published_at) {
                $post->published_at = now();
            }

            $post->save();

            // Create more descriptive flash message
            $action = $post->status === 'published' ? 'published' : 'unpublished';
            session()->flash('message', "Post '{$post->title}' has been {$action} successfully.");

            $this->closeStatusModal();

            // Add a small delay to ensure UI updates are visible
            $this->dispatch('post-status-updated', ['postId' => $this->postToChangeStatus, 'newStatus' => $post->status]);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update post status. Please try again.');
            $this->closeStatusModal();
        }
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->postToChangeStatus = null;
        $this->postToChangeStatusTitle = '';
        $this->newStatus = '';
    }

    public function toggleStatus($postId)
    {
        // This method is now replaced by confirmStatusChange
        $this->confirmStatusChange($postId);
    }

    public function canCreate()
    {
        return Auth::user()->hasPermission('posts.create') || Auth::user()->isSuperAdmin();
    }

    public function canEdit()
    {
        return Auth::user()->hasPermission('posts.edit') || Auth::user()->isSuperAdmin();
    }

    public function canDelete()
    {
        return Auth::user()->hasPermission('posts.delete') || Auth::user()->isSuperAdmin();
    }

    public function render()
    {
        $query = Post::with(['categories'])
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->category, function ($query) {
                $query->whereHas('categories', function ($q) {
                    $q->where('categories.id', $this->category);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $posts = $query->paginate(10);
        $categories = Category::orderBy('name')->get();

        return view('livewire.posts.post-index', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }
}
