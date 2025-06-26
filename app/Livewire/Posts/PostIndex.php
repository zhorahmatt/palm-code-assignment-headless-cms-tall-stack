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

    public function delete($postId)
    {
        $post = Post::findOrFail($postId);
        $post->delete();
        
        session()->flash('message', 'Post deleted successfully.');
    }

    public function toggleStatus($postId)
    {
        $post = Post::findOrFail($postId);
        $post->status = $post->status === 'published' ? 'draft' : 'published';
        $post->save();
        
        session()->flash('message', 'Post status updated successfully.');
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
