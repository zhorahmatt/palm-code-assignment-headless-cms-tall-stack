<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;

class CategoryCreate extends Component
{
    #[Validate('required|string|max:255')]
    public $name = '';
    
    #[Validate('required|string|max:255|unique:categories,slug')]
    public $slug = '';
    
    #[Validate('nullable|string')]
    public $description = '';

    public function updatedName()
    {
        $this->slug = Str::slug($this->name);
    }

    public function save()
    {
        $this->validate();

        try {
            Category::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
            ]);

            session()->flash('message', 'Category created successfully.');
            return $this->redirect(route('categories.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create category. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.categories.category-create');
    }
}
