<?php

namespace App\Livewire\Pages;

use App\Models\Page;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Services\ContentSanitizer;

class PageCreate extends Component
{
    use WithFileUploads;

    public $title = '';
    public $slug = '';
    public $body = ''; // Changed from content to match Page model
    public $status = 'draft';

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:pages,slug',
        'body' => 'required|string', // Changed from content
        'status' => 'required|in:draft,published',
    ];

    public function updatedTitle()
    {
        $this->slug = Str::slug($this->title);
    }

    public function save()
    {
        $this->validate();

        $sanitizer = new ContentSanitizer();

        Page::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $sanitizer->sanitize($this->body), // Changed from content
            'status' => $this->status,
        ]);

        session()->flash('message', 'Page created successfully.');
        return redirect()->route('pages.index');
    }

    public function saveDraft()
    {
        $this->validate();

        $sanitizer = new ContentSanitizer();

        Page::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $sanitizer->sanitize($this->body), // Changed from content
            'status' => 'draft',
        ]);

        session()->flash('message', 'Page saved as draft successfully.');
        return redirect()->route('pages.index');
    }

    public function render()
    {
        return view('livewire.pages.page-create');
    }
}
