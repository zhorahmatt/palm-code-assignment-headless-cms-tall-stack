<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
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
            $query = Post::with(['categories'])
                ->where('status', 'published')
                ->orderBy('published_at', 'desc');

            // Optional pagination
            $perPage = $request->get('per_page', 15);
            $posts = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $posts->items(),
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
                'message' => 'Failed to retrieve posts',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
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

    // Note: store, update, destroy methods are not implemented
    // as this is a read-only API for headless CMS consumption
}
