<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| Why we structure it this way:
| - RESTful design: Standard HTTP methods (GET, POST, PUT, DELETE)
| - Resource controllers: Laravel's built-in REST conventions
| - Versioning: v1 prefix for future API evolution
| - Public access: No authentication required for reading content
*/

// API Version 1 - Grouped routes for better organization
Route::prefix('v1')->group(function () {

    // Posts API - Full CRUD for headless CMS consumption
    Route::apiResource('posts', PostController::class)->only([
        'index',    // GET /api/v1/posts - List all published posts
        'show'      // GET /api/v1/posts/{id} - Get single post
    ]);

    // Pages API - Read-only for public consumption
    Route::apiResource('pages', PageController::class)->only([
        'index',    // GET /api/v1/pages - List all published pages
        'show'      // GET /api/v1/pages/{id} - Get single page
    ]);

    // Categories API - Read-only for public consumption
    Route::apiResource('categories', CategoryController::class)->only([
        'index',    // GET /api/v1/categories - List all categories
        'show'      // GET /api/v1/categories/{id} - Get single category with posts
    ]);

    // Additional endpoints for better API usability
    Route::get('posts/category/{category}', [PostController::class, 'byCategory']); // GET /api/v1/posts/category/{category}
    Route::get('posts/search', [PostController::class, 'search']); // GET /api/v1/posts/search
});
