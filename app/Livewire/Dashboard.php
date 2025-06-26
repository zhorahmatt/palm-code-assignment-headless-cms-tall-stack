<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use App\Models\Page;
use App\Models\Category;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard', [
            'postsCount' => Post::count(), // Total posts in system
            'publishedPostsCount' => Post::published()->count(), // Only published posts
            'draftPostsCount' => Post::draft()->count(), // Only draft posts
            'pagesCount' => Page::count(), // Total pages
            'categoriesCount' => Category::count(), // Total categories
        ]);
    }
}
