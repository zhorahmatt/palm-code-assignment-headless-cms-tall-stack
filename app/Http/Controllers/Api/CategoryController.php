<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories.
     * GET /api/v1/categories
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::withCount(['posts' => function ($query) {
                $query->where('status', 'published');
            }])->orderBy('name');

            // Optional: include posts in response
            if ($request->get('include_posts') === 'true') {
                $query->with(['posts' => function ($query) {
                    $query->where('status', 'published')
                          ->orderBy('published_at', 'desc')
                          ->limit(5); // Limit to recent 5 posts per category
                }]);
            }

            $categories = $query->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified category with its posts.
     * GET /api/v1/categories/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            $category = Category::with(['posts' => function ($query) {
                $query->where('status', 'published')
                      ->orderBy('published_at', 'desc');
            }])
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published');
            }])
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve category',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get posts for a specific category.
     * GET /api/v1/categories/{category}/posts
     */
    public function posts(Request $request, string $category): JsonResponse
    {
        try {
            // Validate query parameters
            $validated = $request->validate([
                'per_page' => 'integer|min:1|max:100',
                'page' => 'integer|min:1',
                'sort' => 'in:title,published_at,created_at',
                'order' => 'in:asc,desc',
            ]);

            // Find category by slug or ID
            $categoryModel = Category::where('slug', $category)
                ->orWhere('id', $category)
                ->firstOrFail();

            $query = $categoryModel->posts()
                ->where('status', 'published')
                ->with(['categories'])
                ->orderBy($validated['sort'] ?? 'published_at', $validated['order'] ?? 'desc');

            $perPage = $validated['per_page'] ?? 15;
            $posts = $query->paginate($perPage);

            return response()->json([
                'data' => $posts->items(),
                'category' => $categoryModel,
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
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve posts for category',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
