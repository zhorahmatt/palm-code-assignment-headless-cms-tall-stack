<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Services\ContentSanitizer;

class PostEdit extends Component
{
    use WithFileUploads;

    public Post $post;
    public $title;
    public $slug;
    public $excerpt;
    public $content;
    public $status;
    public $image; // Changed from featured_image
    public $existing_image;
    public $meta_title;
    public $meta_description;
    public $published_at;
    public $selectedCategories = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'excerpt' => 'nullable|string|max:500',
        'content' => 'required|string',
        'status' => 'required|in:draft,published',
        'image' => 'nullable|image|max:2048', // Changed from featured_image
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:255',
        'published_at' => 'nullable|date',
        'selectedCategories' => 'array',
    ];

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = $post->excerpt;
        $this->content = $post->content;
        $this->status = $post->status;
        $this->existing_image = $post->image; // Changed from featured_image
        $this->meta_title = $post->meta_title;
        $this->meta_description = $post->meta_description;
        $this->published_at = $post->published_at?->format('Y-m-d\TH:i');
        $this->selectedCategories = $post->categories->pluck('id')->toArray();
    }

    public function updatedTitle()
    {
        $this->slug = Str::slug($this->title);
    }

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . $this->post->id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'image' => 'nullable|image|max:2048', // Changed from featured_image
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'selectedCategories' => 'array',
        ];
    }

    public function save()
    {
        $this->validate();

        $sanitizer = new ContentSanitizer(); // Added content sanitization

        $imagePath = $this->existing_image;
        if ($this->image) { // Changed from featured_image
            $imagePath = $this->image->store('posts', 'public');
        }

        $this->post->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $sanitizer->sanitize($this->content), // Added sanitization
            'status' => $this->status,
            'image' => $imagePath, // Changed from featured_image
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'published_at' => $this->status === 'published' ? ($this->published_at ?: now()) : null,
        ]);

        $this->post->categories()->sync($this->selectedCategories);

        session()->flash('message', 'Post updated successfully.');
        return redirect()->route('posts.index');
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();

        return view('livewire.posts.post-edit', [
            'categories' => $categories,
        ]);
    }
}
