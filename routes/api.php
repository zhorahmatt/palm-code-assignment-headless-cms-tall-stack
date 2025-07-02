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
*/

// API Version 1 - Public read-only endpoints
Route::prefix('v1')->name('api.v1.')->group(function () {

    // Apply rate limiting and CORS
    Route::middleware(['throttle:api'])->group(function () {

        // Posts endpoints
        Route::prefix('posts')->name('posts.')->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('index');
            Route::get('/search', [PostController::class, 'search'])->name('search');
            Route::get('/{post}', [PostController::class, 'show'])->name('show');
        });

        // Pages endpoints
        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/', [PageController::class, 'index'])->name('index');
            Route::get('/{page}', [PageController::class, 'show'])->name('show');
        });

        // Categories endpoints
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
            Route::get('/{category}/posts', [CategoryController::class, 'posts'])->name('posts');
        });

        // Additional utility endpoints
        Route::get('/sitemap', [PostController::class, 'sitemap'])->name('sitemap');
        Route::get('/health', function () {
            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'version' => '1.0.0'
            ]);
        })->name('health');
    });
});
