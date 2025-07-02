<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PageController extends Controller
{
    /**
     * Display a listing of published pages.
     * GET /api/v1/pages
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validate query parameters
            $validated = $request->validate([
                'per_page' => 'integer|min:1|max:100',
                'page' => 'integer|min:1',
                'sort' => 'in:title,created_at,updated_at',
                'order' => 'in:asc,desc',
            ]);

            $query = Page::where('status', 'published')
                ->orderBy($validated['sort'] ?? 'title', $validated['order'] ?? 'asc');

            $perPage = $validated['per_page'] ?? 15;
            $pages = $query->paginate($perPage);

            return response()->json([
                'data' => $pages->items(),
                'meta' => [
                    'current_page' => $pages->currentPage(),
                    'last_page' => $pages->lastPage(),
                    'per_page' => $pages->perPage(),
                    'total' => $pages->total(),
                    'from' => $pages->firstItem(),
                    'to' => $pages->lastItem(),
                ],
                'links' => [
                    'first' => $pages->url(1),
                    'last' => $pages->url($pages->lastPage()),
                    'prev' => $pages->previousPageUrl(),
                    'next' => $pages->nextPageUrl(),
                ]
            ], 200, [
                'Content-Type' => 'application/json',
                'X-Total-Count' => $pages->total(),
                'Cache-Control' => 'public, max-age=600', // 10 minutes cache
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
                'message' => 'Failed to retrieve pages',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified published page.
     * GET /api/v1/pages/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            $page = Page::where('status', 'published')
                ->where(function ($query) use ($id) {
                    $query->where('id', $id)
                          ->orWhere('slug', $id);
                })
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $page
            ], 200, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'public, max-age=1800', // 30 minutes cache
                'ETag' => md5($page->updated_at)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found or not published'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve page',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
