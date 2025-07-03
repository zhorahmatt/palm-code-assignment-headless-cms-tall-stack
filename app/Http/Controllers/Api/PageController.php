<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="Local development server"
 * )
 *
 * @OA\Server(
 *     url="https://api.example.com/v1",
 *     description="Production server"
 * )
 *
 * @OA\Schema(
 *     schema="Page",
 *     type="object",
 *     title="Page",
 *     description="Page model representing a static content page",
 *     required={"id", "title", "slug", "content", "status"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Unique identifier for the page",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         maxLength=255,
 *         description="Page title",
 *         example="About Us"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         maxLength=255,
 *         pattern="^[a-z0-9-]+$",
 *         description="URL-friendly version of the title",
 *         example="about-us"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string",
 *         description="Main content of the page (HTML allowed)",
 *         example="<p>This is the content of the about us page with <strong>HTML formatting</strong>.</p>"
 *     ),
 *     @OA\Property(
 *         property="excerpt",
 *         type="string",
 *         nullable=true,
 *         maxLength=500,
 *         description="Brief description or summary of the page",
 *         example="Brief description of the page for previews and listings"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"draft", "published", "archived"},
 *         description="Publication status of the page",
 *         example="published"
 *     ),
 *     @OA\Property(
 *         property="meta_title",
 *         type="string",
 *         nullable=true,
 *         maxLength=60,
 *         description="SEO meta title (recommended: 50-60 characters)",
 *         example="About Us - Your Company Name"
 *     ),
 *     @OA\Property(
 *         property="meta_description",
 *         type="string",
 *         nullable=true,
 *         maxLength=160,
 *         description="SEO meta description (recommended: 150-160 characters)",
 *         example="Learn more about our company, mission, and values. Discover what makes us unique in the industry."
 *     ),
 *     @OA\Property(
 *         property="featured_image",
 *         type="string",
 *         nullable=true,
 *         format="uri",
 *         description="URL to the featured image for the page",
 *         example="/storage/images/pages/about-featured.jpg"
 *     ),
 *     @OA\Property(
 *         property="published_at",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         description="Date and time when the page was published",
 *         example="2024-01-01T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the page was created",
 *         example="2024-01-01T10:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the page was last updated",
 *         example="2024-01-01T14:00:00Z"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *     title="Pagination Metadata",
 *     description="Metadata for paginated responses",
 *     @OA\Property(
 *         property="current_page",
 *         type="integer",
 *         minimum=1,
 *         description="Current page number",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="last_page",
 *         type="integer",
 *         minimum=1,
 *         description="Last available page number",
 *         example=10
 *     ),
 *     @OA\Property(
 *         property="per_page",
 *         type="integer",
 *         minimum=1,
 *         maximum=100,
 *         description="Number of items per page",
 *         example=15
 *     ),
 *     @OA\Property(
 *         property="total",
 *         type="integer",
 *         minimum=0,
 *         description="Total number of items",
 *         example=150
 *     ),
 *     @OA\Property(
 *         property="from",
 *         type="integer",
 *         nullable=true,
 *         minimum=1,
 *         description="First item number on current page",
 *         example=16
 *     ),
 *     @OA\Property(
 *         property="to",
 *         type="integer",
 *         nullable=true,
 *         minimum=1,
 *         description="Last item number on current page",
 *         example=30
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     type="object",
 *     title="Pagination Links",
 *     description="Navigation links for pagination",
 *     @OA\Property(
 *         property="first",
 *         type="string",
 *         nullable=true,
 *         format="uri",
 *         description="URL to the first page",
 *         example="http://localhost:8000/api/v1/pages?page=1"
 *     ),
 *     @OA\Property(
 *         property="last",
 *         type="string",
 *         nullable=true,
 *         format="uri",
 *         description="URL to the last page",
 *         example="http://localhost:8000/api/v1/pages?page=10"
 *     ),
 *     @OA\Property(
 *         property="prev",
 *         type="string",
 *         nullable=true,
 *         format="uri",
 *         description="URL to the previous page",
 *         example="http://localhost:8000/api/v1/pages?page=1"
 *     ),
 *     @OA\Property(
 *         property="next",
 *         type="string",
 *         nullable=true,
 *         format="uri",
 *         description="URL to the next page",
 *         example="http://localhost:8000/api/v1/pages?page=3"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Error Response",
 *     description="Standard error response format",
 *     required={"success", "message"},
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         description="Indicates if the request was successful",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Human-readable error message",
 *         example="An error occurred while processing your request"
 *     ),
 *     @OA\Property(
 *         property="error",
 *         type="string",
 *         description="Error type or category",
 *         example="ValidationError"
 *     ),
 *     @OA\Property(
 *         property="details",
 *         oneOf={
 *             @OA\Schema(type="string"),
 *             @OA\Schema(type="object"),
 *             @OA\Schema(type="array", @OA\Items(type="string"))
 *         },
 *         nullable=true,
 *         description="Additional error details or validation errors"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="NotFoundResponse",
 *     type="object",
 *     title="Not Found Response",
 *     description="Response when a resource is not found",
 *     required={"success", "message"},
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         description="Indicates if the request was successful",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Human-readable error message",
 *         example="Page not found or not published"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     title="Validation Error Response",
 *     description="Response for validation errors",
 *     required={"success", "error", "message", "details"},
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         description="Indicates if the request was successful",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="error",
 *         type="string",
 *         description="Error type",
 *         example="Validation failed"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Human-readable error message",
 *         example="Invalid query parameters"
 *     ),
 *     @OA\Property(
 *         property="details",
 *         type="object",
 *         description="Field-specific validation errors",
 *         additionalProperties={
 *             @OA\Schema(
 *                 type="array",
 *                 @OA\Items(type="string")
 *             )
 *         },
 *         example={
 *             "per_page": {"The per page must be at least 1."},
 *             "sort": {"The selected sort is invalid."}
 *         }
 *     )
 * )
 */
class PageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/pages",
     *     summary="Get all published pages",
     *     description="Retrieve a paginated list of all published pages with flexible sorting and filtering options. This endpoint supports caching and returns comprehensive pagination metadata.",
     *     operationId="getPages",
     *     tags={"Pages"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of pages per page. Controls pagination size for better performance.",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             minimum=1,
     *             maximum=100,
     *             default=15
     *         ),
     *         example=20
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination. Starts from 1.",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             minimum=1,
     *             default=1
     *         ),
     *         example=2
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Field to sort by. Determines the ordering of results.",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"title", "created_at", "updated_at"},
     *             default="title"
     *         ),
     *         example="created_at"
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Sort direction. Use 'asc' for ascending or 'desc' for descending order.",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"},
     *             default="asc"
     *         ),
     *         example="desc"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved paginated list of published pages",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 description="Indicates successful operation",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Array of page objects",
     *                 @OA\Items(ref="#/components/schemas/Page")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 ref="#/components/schemas/PaginationMeta",
     *                 description="Pagination metadata"
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 ref="#/components/schemas/PaginationLinks",
     *                 description="Pagination navigation links"
     *             )
     *         ),
     *         @OA\Header(
     *             header="X-Total-Count",
     *             description="Total number of pages available",
     *             @OA\Schema(type="integer", example=150)
     *         ),
     *         @OA\Header(
     *             header="Cache-Control",
     *             description="Cache control directives for client-side caching",
     *             @OA\Schema(type="string", example="public, max-age=600")
     *         ),
     *         @OA\Header(
     *             header="Content-Type",
     *             description="Response content type",
     *             @OA\Schema(type="string", example="application/json")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error - Invalid query parameters provided",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error - Failed to retrieve pages",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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
                'success' => true,
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
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'message' => 'Invalid query parameters',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
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
     *     description="Retrieve a specific published page by its ID or slug. This endpoint supports both numeric IDs and URL-friendly slugs for flexible access. The response includes caching headers for optimal performance.",
     *     operationId="getPage",
     *     tags={"Pages"},
     *     @OA\Parameter(
     *         name="page",
     *         in="path",
     *         description="Page identifier - can be either a numeric ID or a URL slug",
     *         required=true,
     *         @OA\Schema(
     *             oneOf={
     *                 @OA\Schema(type="integer", minimum=1, example=1),
     *                 @OA\Schema(type="string", pattern="^[a-z0-9-]+$", example="about-us")
     *             }
     *         ),
     *         @OA\Examples(
     *             example="by_id",
     *             summary="Access by ID",
     *             value="1"
     *         ),
     *         @OA\Examples(
     *             example="by_slug",
     *             summary="Access by slug",
     *             value="about-us"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved the requested page",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 description="Indicates successful operation",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Page",
     *                 description="The requested page object"
     *             )
     *         ),
     *         @OA\Header(
     *             header="Cache-Control",
     *             description="Cache control directives for extended caching",
     *             @OA\Schema(type="string", example="public, max-age=1800")
     *         ),
     *         @OA\Header(
     *             header="ETag",
     *             description="Entity tag for conditional requests and cache validation",
     *             @OA\Schema(type="string", example="5d41402abc4b2a76b9719d911017c592")
     *         ),
     *         @OA\Header(
     *             header="Content-Type",
     *             description="Response content type",
     *             @OA\Schema(type="string", example="application/json")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found or not published",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error - Failed to retrieve page",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
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
                'ETag' => '"' . md5($page->updated_at) . '"'
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
