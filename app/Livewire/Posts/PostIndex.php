<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

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

    public function deletePost($postId)
    {
        try {
            $post = Post::findOrFail($postId);
            $post->delete();

            session()->flash('message', 'Post deleted successfully.');

            // Refresh the component to update the list
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete post. Please try again.');
        }
    }

    public function toggleStatus($postId)
    {
        try {
            $post = Post::findOrFail($postId);
            $oldStatus = $post->status;
            $post->status = $post->status === 'published' ? 'draft' : 'published';

            // Set published_at when publishing
            if ($post->status === 'published' && !$post->published_at) {
                $post->published_at = now();
            }

            $post->save();

            // Create more descriptive flash message
            $action = $post->status === 'published' ? 'published' : 'unpublished';
            session()->flash('message', "Post '{$post->title}' has been {$action} successfully.");

            // Add a small delay to ensure UI updates are visible
            $this->dispatch('post-status-updated', ['postId' => $postId, 'newStatus' => $post->status]);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update post status. Please try again.');
        }
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
