<?php

namespace App\Livewire\Pages;

use App\Models\Page;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class PageIndex extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $status = '';

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
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

    public function deletePage($pageId)
    {
        try {
            $page = Page::findOrFail($pageId);
            $page->delete();

            session()->flash('message', 'Page deleted successfully.');

            // Refresh the component to update the list
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete page. Please try again.');
        }
    }

    public function toggleStatus($pageId)
    {
        try {
            $page = Page::findOrFail($pageId);
            $oldStatus = $page->status;
            $page->status = $page->status === 'published' ? 'draft' : 'published';

            $page->save();

            // Create more descriptive flash message
            $action = $page->status === 'published' ? 'published' : 'unpublished';
            session()->flash('message', "Page '{$page->title}' has been {$action} successfully.");

            // Add event dispatch for better UI updates
            $this->dispatch('page-status-updated', ['pageId' => $pageId, 'newStatus' => $page->status]);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update page status. Please try again.');
        }
    }

    public function render()
    {
        $query = Page::when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('body', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $pages = $query->paginate(10);

        return view('livewire.pages.page-index', [
            'pages' => $pages,
        ]);
    }
}
