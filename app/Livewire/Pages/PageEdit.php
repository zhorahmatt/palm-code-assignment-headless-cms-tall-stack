<?php

namespace App\Livewire\Pages;

use App\Models\Page;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Services\ContentSanitizer;

class PageEdit extends Component
{
    use WithFileUploads;

    public Page $page;
    public $title;
    public $slug;
    public $body;
    public $status;
    public $featured_image;
    public $existing_image;
    public $meta_title;
    public $meta_description;
    public $published_at;

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'body' => 'required|string',
        'status' => 'required|in:draft,published',
        'featured_image' => 'nullable|image|max:2048',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:255',
        'published_at' => 'nullable|date',
    ];

    public function mount(Page $page)
    {
        $this->page = $page;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->body = $page->body;
        $this->status = $page->status;
        $this->existing_image = $page->featured_image;
        $this->meta_title = $page->meta_title;
        $this->meta_description = $page->meta_description;
        $this->published_at = $page->published_at?->format('Y-m-d\\TH:i');
    }

    public function updatedTitle()
    {
        $this->slug = Str::slug($this->title);
    }

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $this->page->id,
            'body' => 'required|string',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
        ];
    }

    public function save()
    {
        $this->validate();

        $sanitizer = new ContentSanitizer();

        $imagePath = $this->existing_image;
        if ($this->featured_image) {
            $imagePath = $this->featured_image->store('pages', 'public');
        }

        $this->page->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $sanitizer->sanitize($this->body),
            'status' => $this->status,
            'featured_image' => $imagePath,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'published_at' => $this->status === 'published' ? ($this->published_at ?: now()) : null,
        ]);

        session()->flash('message', 'Page updated successfully.');
        return redirect()->route('pages.index');
    }

    public function saveDraft()
    {
        $this->validate();

        $sanitizer = new ContentSanitizer();

        $imagePath = $this->existing_image;
        if ($this->featured_image) {
            $imagePath = $this->featured_image->store('pages', 'public');
        }

        $this->page->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $sanitizer->sanitize($this->body),
            'status' => 'draft',
            'featured_image' => $imagePath,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'published_at' => null,
        ]);

        session()->flash('message', 'Page saved as draft successfully.');
        return redirect()->route('pages.index');
    }

    public function deletePage()
    {
        $this->page->delete();

        session()->flash('message', 'Page deleted successfully.');
        return redirect()->route('pages.index');
    }

    public function removeCurrentImage()
    {
        $this->existing_image = null;
    }

    public function render()
    {
        return view('livewire.pages.page-edit');
    }
}
