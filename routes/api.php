<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API Version 1
Route::prefix('v1')->name('api.v1.')->group(function () {

    // Apply rate limiting
    Route::middleware(['throttle:api'])->group(function () {

        // Authentication endpoints (public)
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/login', [AuthController::class, 'login'])->name('login');
        });

        // Public endpoints (no authentication required)
        Route::prefix('public')->name('public.')->group(function () {
            // Health check
            Route::get('/health', function () {
                return response()->json([
                    'status' => 'healthy',
                    'timestamp' => now()->toISOString(),
                    'version' => '1.0.0'
                ]);
            })->name('health');

            // Public sitemap
            Route::get('/sitemap', [PostController::class, 'sitemap'])->name('sitemap');
        });

        // Protected endpoints (authentication required)
        Route::middleware(['auth:sanctum'])->group(function () {

            // Authentication endpoints (protected)
            Route::prefix('auth')->name('auth.')->group(function () {
                Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
                Route::get('/user', [AuthController::class, 'user'])->name('user');
            });

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
        });
    });
});
