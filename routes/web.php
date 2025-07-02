<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

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

    // Users Management Routes (RBAC)
    Route::get('/users', App\Livewire\Users\UserIndex::class)->name('users.index');
    Route::get('/users/create', App\Livewire\Users\UserCreate::class)->name('users.create');
    Route::get('/users/{user}/edit', App\Livewire\Users\UserEdit::class)->name('users.edit');

    // Roles Management Routes
    Route::get('/roles', App\Livewire\Roles\RoleIndex::class)->name('roles.index');
    Route::get('/roles/create', App\Livewire\Roles\RoleCreate::class)->name('roles.create');
    Route::get('/roles/{role}/edit', App\Livewire\Roles\RoleEdit::class)->name('roles.edit');

    // Permissions Management Routes
    Route::get('/permissions', App\Livewire\Permissions\PermissionIndex::class)->name('permissions.index');
    Route::get('/permissions/create', App\Livewire\Permissions\PermissionCreate::class)->name('permissions.create');
    Route::get('/permissions/{permission}/edit', App\Livewire\Permissions\PermissionEdit::class)->name('permissions.edit');
});

require __DIR__.'/auth.php';
