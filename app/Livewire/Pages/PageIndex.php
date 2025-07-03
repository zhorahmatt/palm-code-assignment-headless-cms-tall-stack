<?php

namespace App\Livewire\Pages;

use App\Models\Page;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class PageIndex extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $status = '';

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Modal state properties
    public $showDeleteModal = false;
    public $pageToDelete = null;
    public $pageToDeleteTitle = '';
    
    // Status change modal properties
    public $showStatusModal = false;
    public $pageToChangeStatus = null;
    public $pageToChangeStatusTitle = '';
    public $newStatus = '';

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

    public function confirmDelete($pageId)
    {
        $page = Page::find($pageId);
        if ($page) {
            $this->pageToDelete = $pageId;
            $this->pageToDeleteTitle = $page->title;
            $this->showDeleteModal = true;
        }
    }

    public function deletePage()
    {
        try {
            $page = Page::findOrFail($this->pageToDelete);
            $page->delete();

            session()->flash('message', 'Page deleted successfully.');
            $this->closeDeleteModal();

            // Refresh the component to update the list
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete page. Please try again.');
            $this->closeDeleteModal();
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->pageToDelete = null;
        $this->pageToDeleteTitle = '';
    }

    public function confirmStatusChange($pageId)
    {
        $page = Page::find($pageId);
        if ($page) {
            $this->pageToChangeStatus = $pageId;
            $this->pageToChangeStatusTitle = $page->title;
            $this->newStatus = $page->status === 'published' ? 'draft' : 'published';
            $this->showStatusModal = true;
        }
    }

    public function changePageStatus()
    {
        try {
            $page = Page::findOrFail($this->pageToChangeStatus);
            $page->status = $this->newStatus;
            $page->save();

            // Create more descriptive flash message
            $action = $page->status === 'published' ? 'published' : 'unpublished';
            session()->flash('message', "Page '{$page->title}' has been {$action} successfully.");

            $this->closeStatusModal();

            // Add event dispatch for better UI updates
            $this->dispatch('page-status-updated', ['pageId' => $this->pageToChangeStatus, 'newStatus' => $page->status]);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update page status. Please try again.');
            $this->closeStatusModal();
        }
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->pageToChangeStatus = null;
        $this->pageToChangeStatusTitle = '';
        $this->newStatus = '';
    }

    public function toggleStatus($pageId)
    {
        // This method is now replaced by confirmStatusChange
        $this->confirmStatusChange($pageId);
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
