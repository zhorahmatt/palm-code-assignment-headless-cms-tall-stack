<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @OA\Schema(
 *     schema="Page",
 *     type="object",
 *     title="Page",
 *     description="Page model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="About Us"),
 *     @OA\Property(property="slug", type="string", example="about-us"),
 *     @OA\Property(property="content", type="string", example="This is the content of the about us page"),
 *     @OA\Property(property="excerpt", type="string", nullable=true, example="Brief description of the page"),
 *     @OA\Property(property="status", type="string", example="published"),
 *     @OA\Property(property="meta_title", type="string", nullable=true, example="About Us - SEO Title"),
 *     @OA\Property(property="meta_description", type="string", nullable=true, example="SEO description for about us page"),
 *     @OA\Property(property="featured_image", type="string", nullable=true, example="/images/about-featured.jpg"),
 *     @OA\Property(property="published_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T14:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     type="object",
 *     title="Pagination Links",
 *     description="Pagination navigation links",
 *     @OA\Property(property="first", type="string", nullable=true, example="http://localhost/api/v1/pages?page=1"),
 *     @OA\Property(property="last", type="string", nullable=true, example="http://localhost/api/v1/pages?page=10"),
 *     @OA\Property(property="prev", type="string", nullable=true, example="http://localhost/api/v1/pages?page=1"),
 *     @OA\Property(property="next", type="string", nullable=true, example="http://localhost/api/v1/pages?page=3")
 * )
 */
class PageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/pages",
     *     summary="Get all published pages",
     *     description="Retrieve a paginated list of all published pages with sorting options",
     *     operationId="getPages",
     *     tags={"Pages"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of pages per page (max 100)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort field",
     *         required=false,
     *         @OA\Schema(type="string", enum={"title", "created_at", "updated_at"}, default="title")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Sort order",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Page")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=150),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=15)
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 ref="#/components/schemas/PaginationLinks"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Total-Count",
     *             description="Total number of pages",
     *             @OA\Schema(type="integer")
     *         ),
     *         @OA\Header(
     *             header="Cache-Control",
     *             description="Cache control header",
     *             @OA\Schema(type="string", example="public, max-age=600")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation failed"),
     *             @OA\Property(property="message", type="string", example="Invalid query parameters"),
     *             @OA\Property(
     *                 property="details",
     *                 type="object",
     *                 example={"per_page": {"The per page must be at least 1."}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal server error"),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve pages"),
     *             @OA\Property(property="details", type="string", nullable=true, example="Detailed error message")
     *         )
     *     )
     * )
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
                'success' => true, // Add this for consistency
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
                'success' => false, // Add this for consistency
                'error' => 'Validation failed',
                'message' => 'Invalid query parameters',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, // Add this for consistency
                'error' => 'Internal server error',
                'message' => 'Failed to retrieve pages',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/pages/{page}",
     *     summary="Get a specific page",
     *     description="Retrieve a specific published page by ID or slug",
     *     operationId="getPage",
     *     tags={"Pages"},
     *     @OA\Parameter(
     *         name="page",
     *         in="path",
     *         description="Page ID or slug",
     *         required=true,
     *         @OA\Schema(type="string", example="about-us")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Page"
     *             )
     *         ),
     *         @OA\Header(
     *             header="Cache-Control",
     *             description="Cache control header",
     *             @OA\Schema(type="string", example="public, max-age=1800")
     *         ),
     *         @OA\Header(
     *             header="ETag",
     *             description="Entity tag for caching",
     *             @OA\Schema(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Page not found or not published")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve page"),
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
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
