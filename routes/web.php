<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Admin Routes - Protected by authentication
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', App\Livewire\Dashboard::class)->name('dashboard');

    // Posts Management Routes
    Route::get('/posts', App\Livewire\Posts\PostIndex::class)->name('posts.index');
    Route::get('/posts/create', App\Livewire\Posts\PostCreate::class)->name('posts.create');
    Route::get('/posts/{post}/edit', App\Livewire\Posts\PostEdit::class)->name('posts.edit');

    // Categories Management Routes
    Route::get('/categories', App\Livewire\Categories\CategoryIndex::class)->name('categories.index');
    Route::get('/categories/create', App\Livewire\Categories\CategoryCreate::class)->name('categories.create');
    Route::get('/categories/{category}/edit', App\Livewire\Categories\CategoryEdit::class)->name('categories.edit');

    // Pages Management Routes
    Route::get('/pages', App\Livewire\Pages\PageIndex::class)->name('pages.index');
    Route::get('/pages/create', App\Livewire\Pages\PageCreate::class)->name('pages.create');
    Route::get('/pages/{page}/edit', App\Livewire\Pages\PageEdit::class)->name('pages.edit');
});

require __DIR__.'/auth.php';
