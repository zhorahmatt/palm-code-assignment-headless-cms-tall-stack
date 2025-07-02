<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class CategoryIndex extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';
    
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
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

    public function delete($categoryId)
    {
        try {
            $category = Category::findOrFail($categoryId);
            
            // Check if category has posts
            if ($category->posts()->count() > 0) {
                session()->flash('error', 'Cannot delete category with associated posts.');
                return;
            }
            
            $category->delete();
            session()->flash('message', 'Category deleted successfully.');

            // Refresh the component to update the list
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete category. Please try again.');
        }
    }

    public function render()
    {
        $query = Category::withCount('posts')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $categories = $query->paginate(10);

        return view('livewire.categories.category-index', [
            'categories' => $categories,
        ]);
    }
}
