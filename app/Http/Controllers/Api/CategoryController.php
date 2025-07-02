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

    // Note: store, update, destroy methods are not implemented
    // as this is a read-only API for headless CMS consumption
}
