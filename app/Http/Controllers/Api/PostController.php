<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostController extends Controller
{
    /**
     * Display a listing of published posts.
     * GET /api/v1/posts
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validate query parameters
            $validated = $request->validate([
                'per_page' => 'integer|min:1|max:100',
                'page' => 'integer|min:1',
                'sort' => 'in:title,published_at,created_at',
                'order' => 'in:asc,desc',
                'category' => 'string|exists:categories,slug',
            ]);

            $query = Post::with(['categories'])
                ->where('status', 'published')
                ->orderBy($validated['sort'] ?? 'published_at', $validated['order'] ?? 'desc');

            // Filter by category if provided
            if (isset($validated['category'])) {
                $query->whereHas('categories', function ($q) use ($validated) {
                    $q->where('slug', $validated['category']);
                });
            }

            $perPage = $validated['per_page'] ?? 15;
            $posts = $query->paginate($perPage);

            return response()->json([
                'data' => $posts->items(),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'from' => $posts->firstItem(),
                    'to' => $posts->lastItem(),
                ],
                'links' => [
                    'first' => $posts->url(1),
                    'last' => $posts->url($posts->lastPage()),
                    'prev' => $posts->previousPageUrl(),
                    'next' => $posts->nextPageUrl(),
                ]
            ], 200, [
                'Content-Type' => 'application/json',
                'X-Total-Count' => $posts->total(),
                'Cache-Control' => 'public, max-age=300', // 5 minutes cache
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => 'Invalid query parameters',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Failed to retrieve posts',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified published post.
     * GET /api/v1/posts/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            $post = Post::with(['categories'])
                ->where('status', 'published')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $post
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found or not published'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve post',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get posts by category.
     * GET /api/v1/posts/category/{category}
     */
    public function byCategory(string $category): JsonResponse
    {
        try {
            // Find category by slug or ID
            $categoryModel = Category::where('slug', $category)
                ->orWhere('id', $category)
                ->firstOrFail();

            $posts = Post::with(['categories'])
                ->whereHas('categories', function ($query) use ($categoryModel) {
                    $query->where('categories.id', $categoryModel->id);
                })
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $posts->items(),
                'category' => $categoryModel,
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve posts by category',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Search posts by title and content.
     * GET /api/v1/posts/search?q=search_term
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $searchTerm = $request->get('q');

            if (empty($searchTerm)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search term is required'
                ], 400);
            }

            $posts = Post::with(['categories'])
                ->where('status', 'published')
                ->where(function ($query) use ($searchTerm) {
                    $query->where('title', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('content', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('excerpt', 'LIKE', "%{$searchTerm}%");
                })
                ->orderBy('published_at', 'desc')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $posts->items(),
                'search_term' => $searchTerm,
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search posts',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get recent posts.
     * GET /api/v1/posts/recent
     */
    public function recent(): JsonResponse
    {
        try {
            $posts = Post::with(['categories'])
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $posts
            ], 200, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'public, max-age=300', // 5 minutes cache
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve recent posts',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get sitemap data.
     * GET /api/v1/sitemap
     */
    public function sitemap(): JsonResponse
    {
        try {
            $posts = Post::select(['id', 'slug', 'title', 'updated_at'])
                ->where('status', 'published')
                ->orderBy('updated_at', 'desc')
                ->get();

            $pages = Page::select(['id', 'slug', 'title', 'updated_at'])
                ->where('status', 'published')
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'posts' => $posts,
                    'pages' => $pages
                ],
                'generated_at' => now()->toISOString()
            ], 200, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'public, max-age=3600', // 1 hour cache
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate sitemap',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
