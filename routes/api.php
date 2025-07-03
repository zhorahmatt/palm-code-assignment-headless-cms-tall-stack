<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;

/**
 * @OA\Info(
 *     title="Headless CMS API",
 *     version="1.0.0",
 *     description="A comprehensive headless CMS API for managing posts, pages, categories, and user authentication. This API provides full CRUD operations and search functionality for content management.",
 *     @OA\Contact(
 *         name="API Support",
 *         email="work.rahmathidayat@gmail.com",
 *         url="https://github.com/your-repo"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * @OA\Server(
 *     url="{protocol}://{host}/api/v1",
 *     description="API Version 1",
 *     @OA\ServerVariable(
 *         serverVariable="protocol",
 *         enum={"http", "https"},
 *         default="https"
 *     ),
 *     @OA\ServerVariable(
 *         serverVariable="host",
 *         default="localhost:8000"
 *     )
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token",
 *     description="Laravel Sanctum token authentication. Include 'Bearer {token}' in Authorization header."
 * )
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication and token management"
 * )
 * @OA\Tag(
 *     name="Posts",
 *     description="Blog posts management and retrieval"
 * )
 * @OA\Tag(
 *     name="Pages",
 *     description="Static pages management and retrieval"
 * )
 * @OA\Tag(
 *     name="Categories",
 *     description="Content categories management and retrieval"
 * )
 * @OA\Tag(
 *     name="Utility",
 *     description="Utility endpoints like health check and sitemap"
 * )
 */

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here are the API routes for the headless CMS. All routes are versioned
| and include proper authentication and rate limiting.
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
