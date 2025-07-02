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
            $query = Page::where('status', 'published')
                ->orderBy('title');

            // Optional pagination
            $perPage = $request->get('per_page', 15);
            $pages = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $pages->items(),
                'meta' => [
                    'current_page' => $pages->currentPage(),
                    'last_page' => $pages->lastPage(),
                    'per_page' => $pages->perPage(),
                    'total' => $pages->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pages',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
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
                ->where('id', $id)
                ->orWhere('slug', $id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $page
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

    // Note: store, update, destroy methods are not implemented
    // as this is a read-only API for headless CMS consumption
}
