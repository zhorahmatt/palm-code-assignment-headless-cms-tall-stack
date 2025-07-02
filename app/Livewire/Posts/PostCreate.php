<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Services\ContentSanitizer;

class PostCreate extends Component
{
    use WithFileUploads;

    public $title = '';
    public $slug = '';
    public $excerpt = '';
    public $content = '';
    public $status = 'draft';
    public $image; // Changed from featured_image
    public $meta_title = '';
    public $meta_description = '';
    public $published_at;
    public $selectedCategories = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:posts,slug',
        'excerpt' => 'nullable|string|max:500',
        'content' => 'required|string',
        'status' => 'required|in:draft,published',
        'image' => 'nullable|image|max:2048', // Changed from featured_image
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:255',
        'published_at' => 'nullable|date',
        'selectedCategories' => 'array',
    ];

    public function updatedTitle()
    {
        $this->slug = Str::slug($this->title);
    }

    public function save()
    {
        $this->validate();

        $sanitizer = new ContentSanitizer();

        $imagePath = null;
        if ($this->image) { // Changed from featured_image
            $imagePath = $this->image->store('posts', 'public');
        }

        $post = Post::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $sanitizer->sanitize($this->content),
            'status' => $this->status,
            'image' => $imagePath, // Changed from featured_image
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'published_at' => $this->status === 'published' ? ($this->published_at ?: now()) : null,
            'author_id' => auth()->id(),
        ]);

        if (!empty($this->selectedCategories)) {
            $post->categories()->attach($this->selectedCategories);
        }

        session()->flash('message', 'Post created successfully.');
        return redirect()->route('posts.index');
    }

    public function saveDraft()
    {
        $this->validate();

        $sanitizer = new ContentSanitizer();

        $imagePath = null;
        if ($this->image) { // Changed from featured_image
            $imagePath = $this->image->store('posts', 'public');
        }

        $post = Post::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $sanitizer->sanitize($this->content),
            'status' => 'draft',
            'image' => $imagePath, // Changed from featured_image
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'published_at' => null,
            'author_id' => auth()->id(),
        ]);

        if (!empty($this->selectedCategories)) {
            $post->categories()->attach($this->selectedCategories);
        }

        session()->flash('message', 'Post saved as draft successfully.');
        return redirect()->route('posts.index');
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();

        return view('livewire.posts.post-create', [
            'categories' => $categories,
        ]);
    }
}
