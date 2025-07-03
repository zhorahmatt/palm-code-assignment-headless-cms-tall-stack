<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;

class CategoryEdit extends Component
{
    public Category $category;
    
    #[Validate('required|string|max:255')]
    public $name = '';
    
    #[Validate('required|string|max:255')]
    public $slug = '';
    
    #[Validate('nullable|string')]
    public $description = '';

    public function mount(Category $category)
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
    }

    public function updatedName()
    {
        $this->slug = Str::slug($this->name);
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $this->category->id,
            'description' => 'nullable|string',
        ];
    }

    public function update()
    {
        $this->validate();

        try {
            $this->category->update([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
            ]);

            session()->flash('message', 'Category updated successfully.');
            return $this->redirect(route('categories.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update category. Please try again.');
        }
    }

    public function delete()
    {
        try {
            // Check if category has posts
            if ($this->category->posts()->count() > 0) {
                session()->flash('error', 'Cannot delete category with associated posts.');
                return;
            }

            $this->category->delete();
            session()->flash('message', 'Category deleted successfully.');
            return $this->redirect(route('categories.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete category. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.categories.category-edit');
    }
}
