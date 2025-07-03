<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Page;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     title="Headless CMS API",
 *     version="1.0.0",
 *     description="A comprehensive headless CMS API for managing posts, pages, and categories with full CRUD operations, search functionality, and content management features.",
 *     @OA\Contact(
 *         email="work.rahmathidayat@gmail.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * @OA\Server(
 *     url="/api/v1",
 *     description="API Version 1 - Production"
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="API Version 1 - Development"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Laravel Sanctum token authentication. Include the token in the Authorization header as 'Bearer {token}'"
 * )
 *
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     title="Post",
 *     description="Blog post with content and metadata",
 *     required={"id", "title", "slug", "content", "status"},
 *     @OA\Property(property="id", type="integer", description="Unique post identifier", example=1),
 *     @OA\Property(property="title", type="string", description="Post title", example="Getting Started with Laravel", maxLength=255),
 *     @OA\Property(property="slug", type="string", description="URL-friendly post identifier", example="getting-started-with-laravel", maxLength=255),
 *     @OA\Property(property="excerpt", type="string", nullable=true, description="Brief post summary", example="Learn the basics of Laravel framework in this comprehensive guide."),
 *     @OA\Property(property="content", type="string", description="Full post content in HTML format", example="<p>Laravel is a powerful PHP framework...</p>"),
 *     @OA\Property(property="status", type="string", enum={"draft", "published", "archived"}, description="Publication status", example="published"),
 *     @OA\Property(property="featured_image", type="string", nullable=true, description="URL to featured image", example="/storage/images/laravel-guide.jpg"),
 *     @OA\Property(property="meta_title", type="string", nullable=true, description="SEO meta title", example="Laravel Guide - Complete Tutorial", maxLength=255),
 *     @OA\Property(property="meta_description", type="string", nullable=true, description="SEO meta description", example="Complete guide to Laravel framework with examples and best practices.", maxLength=160),
 *     @OA\Property(property="author_name", type="string", nullable=true, description="Post author name", example="John Doe", maxLength=255),
 *     @OA\Property(property="published_at", type="string", format="date-time", nullable=true, description="Publication timestamp", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp", example="2024-01-01T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", example="2024-01-01T14:00:00Z"),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         description="Associated categories",
 *         @OA\Items(ref="#/components/schemas/Category")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     description="Content category for organizing posts",
 *     required={"id", "name", "slug"},
 *     @OA\Property(property="id", type="integer", description="Unique category identifier", example=1),
 *     @OA\Property(property="name", type="string", description="Category name", example="Technology", maxLength=255),
 *     @OA\Property(property="slug", type="string", description="URL-friendly category identifier", example="technology", maxLength=255),
 *     @OA\Property(property="description", type="string", nullable=true, description="Category description", example="Technology related posts and tutorials"),
 *     @OA\Property(property="posts_count", type="integer", description="Number of published posts in this category", example=15),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp", example="2024-01-01T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", example="2024-01-01T14:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *     title="Pagination Metadata",
 *     description="Pagination information for list responses",
 *     @OA\Property(property="current_page", type="integer", description="Current page number", example=1),
 *     @OA\Property(property="last_page", type="integer", description="Total number of pages", example=10),
 *     @OA\Property(property="per_page", type="integer", description="Items per page", example=15),
 *     @OA\Property(property="total", type="integer", description="Total number of items", example=150),
 *     @OA\Property(property="from", type="integer", nullable=true, description="First item number on current page", example=1),
 *     @OA\Property(property="to", type="integer", nullable=true, description="Last item number on current page", example=15)
 * )
 *
 * @OA\Schema(
 *     schema="PaginationLinks",
 *     type="object",
 *     title="Pagination Links",
 *     description="Navigation links for pagination",
 *     @OA\Property(property="first", type="string", nullable=true, description="URL to first page", example="/api/v1/posts?page=1"),
 *     @OA\Property(property="last", type="string", nullable=true, description="URL to last page", example="/api/v1/posts?page=10"),
 *     @OA\Property(property="prev", type="string", nullable=true, description="URL to previous page", example="/api/v1/posts?page=1"),
 *     @OA\Property(property="next", type="string", nullable=true, description="URL to next page", example="/api/v1/posts?page=3")
 * )
 *
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     title="Success Response",
 *     description="Standard success response wrapper",
 *     @OA\Property(property="success", type="boolean", description="Request success status", example=true),
 *     @OA\Property(property="message", type="string", nullable=true, description="Success message", example="Operation completed successfully")
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Error Response",
 *     description="Standard error response",
 *     @OA\Property(property="success", type="boolean", description="Request success status", example=false),
 *     @OA\Property(property="message", type="string", description="Error message", example="An error occurred"),
 *     @OA\Property(property="error_code", type="string", nullable=true, description="Specific error code", example="VALIDATION_ERROR"),
 *     @OA\Property(property="error", type="string", nullable=true, description="Detailed error message (debug mode only)", example="Detailed error message")
 * )
 *
 * @OA\Schema(
 *     schema="UnauthorizedResponse",
 *     type="object",
 *     title="Unauthorized Response",
 *     description="Authentication required response",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Unauthenticated"),
 *     @OA\Property(property="error_code", type="string", example="AUTHENTICATION_REQUIRED")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     title="Validation Error Response",
 *     description="Validation error response with field details",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Validation failed"),
 *     @OA\Property(property="error_code", type="string", example="VALIDATION_ERROR"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         description="Field-specific validation errors",
 *         example={"per_page": {"The per page must be at least 1."}}
 *     )
 * )
 */
class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/posts",
     *     summary="List all published posts",
     *     description="Retrieve a paginated list of all published blog posts with their associated categories. Posts are ordered by publication date (newest first) by default. Supports pagination and filtering.",
     *     operationId="listPosts",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of posts to return per page (1-100)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=15),
     *         example=20
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number to retrieve",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, default=1),
     *         example=1
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string", enum={"title", "published_at", "created_at", "updated_at"}, default="published_at"),
     *         example="published_at"
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Sort order",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc"),
     *         example="desc"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Posts retrieved successfully",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         description="Array of post objects",
     *                         @OA\Items(ref="#/components/schemas/Post")
     *                     ),
     *                     @OA\Property(
     *                         property="meta",
     *                         ref="#/components/schemas/PaginationMeta"
     *                     ),
     *                     @OA\Property(
     *                         property="links",
     *                         ref="#/components/schemas/PaginationLinks"
     *                     )
     *                 )
     *             }
     *         ),
     *         @OA\Header(
     *             header="X-Total-Count",
     *             description="Total number of posts",
     *             @OA\Schema(type="integer")
     *         ),
     *         @OA\Header(
     *             header="Cache-Control",
     *             description="Cache control header",
     *             @OA\Schema(type="string", example="public, max-age=300")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication required",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid query parameters",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
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
                'sort' => 'in:title,published_at,created_at,updated_at',
                'order' => 'in:asc,desc',
            ]);

            $query = Post::where('status', 'published')
                ->with(['categories'])
                ->orderBy(
                    $validated['sort'] ?? 'published_at',
                    $validated['order'] ?? 'desc'
                );

            $perPage = $validated['per_page'] ?? 15;
            $posts = $query->paginate($perPage);

            return response()->json([
                'success' => true,
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
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'error_code' => 'VALIDATION_ERROR',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve posts',
                'error_code' => 'GENERAL_ERROR',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/posts/{post}",
     *     summary="Get a specific post",
     *     description="Retrieve a single published post by its ID or slug, including all associated categories and metadata. Returns 404 if post is not found or not published.",
     *     operationId="getPost",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         description="Post ID or slug to retrieve",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         example="getting-started-with-laravel"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post retrieved successfully",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         ref="#/components/schemas/Post"
     *                     )
     *                 )
     *             }
     *         ),
     *         @OA\Header(
     *             header="Cache-Control",
     *             description="Cache control header",
     *             @OA\Schema(type="string", example="public, max-age=3600")
     *         ),
     *         @OA\Header(
     *             header="ETag",
     *             description="Entity tag for caching",
     *             @OA\Schema(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication required",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found or not published",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ErrorResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(property="message", example="Post not found or not published"),
     *                     @OA\Property(property="error_code", example="POST_NOT_FOUND")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $post = Post::where('status', 'published')
                ->with(['categories'])
                ->where(function ($query) use ($id) {
                    $query->where('id', $id)->orWhere('slug', $id);
                })
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $post
            ], 200, [
                'Cache-Control' => 'public, max-age=3600', // 1 hour cache
                'ETag' => md5($post->updated_at)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found or not published',
                'error_code' => 'POST_NOT_FOUND'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve post',
                'error_code' => 'GENERAL_ERROR',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/posts/search",
     *     summary="Search posts",
     *     description="Search for published posts by title, content, or excerpt using full-text search. Results are paginated and ordered by relevance and publication date. Minimum search term length is 1 character.",
     *     operationId="searchPosts",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query string (minimum 1 character)",
     *         required=true,
     *         @OA\Schema(type="string", minLength=1, maxLength=255),
     *         example="Laravel tutorial"
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results per page (1-100)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=15),
     *         example=10
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number to retrieve",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, default=1),
     *         example=1
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort field for results",
     *         required=false,
     *         @OA\Schema(type="string", enum={"relevance", "published_at", "title"}, default="relevance"),
     *         example="relevance"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search completed successfully",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         description="Array of matching posts",
     *                         @OA\Items(ref="#/components/schemas/Post")
     *                     ),
     *                     @OA\Property(property="search_term", type="string", description="The search query used", example="Laravel tutorial"),
     *                     @OA\Property(property="total_results", type="integer", description="Total number of matching posts", example=42),
     *                     @OA\Property(
     *                         property="meta",
     *                         ref="#/components/schemas/PaginationMeta"
     *                     ),
     *                     @OA\Property(
     *                         property="links",
     *                         ref="#/components/schemas/PaginationLinks"
     *                     )
     *                 )
     *             }
     *         ),
     *         @OA\Header(
     *             header="X-Search-Time",
     *             description="Search execution time in milliseconds",
     *             @OA\Schema(type="number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid search parameters",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ErrorResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(property="message", example="Search term is required"),
     *                     @OA\Property(property="error_code", example="INVALID_SEARCH_PARAMS")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication required",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        
        try {
            // Validate search parameters
            $validated = $request->validate([
                'q' => 'required|string|min:1|max:255',
                'per_page' => 'integer|min:1|max:100',
                'page' => 'integer|min:1',
                'sort' => 'in:relevance,published_at,title',
            ]);

            $searchTerm = $validated['q'];
            $perPage = $validated['per_page'] ?? 15;
            $sort = $validated['sort'] ?? 'relevance';

            $query = Post::where('status', 'published')
                ->with(['categories'])
                ->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('content', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('excerpt', 'LIKE', "%{$searchTerm}%");
                });

            // Apply sorting
            switch ($sort) {
                case 'published_at':
                    $query->orderBy('published_at', 'desc');
                    break;
                case 'title':
                    $query->orderBy('title', 'asc');
                    break;
                case 'relevance':
                default:
                    // Simple relevance: title matches first, then content, then excerpt
                    $query->orderByRaw("CASE 
                        WHEN title LIKE '%{$searchTerm}%' THEN 1 
                        WHEN content LIKE '%{$searchTerm}%' THEN 2 
                        WHEN excerpt LIKE '%{$searchTerm}%' THEN 3 
                        ELSE 4 END")
                        ->orderBy('published_at', 'desc');
                    break;
            }

            $posts = $query->paginate($perPage);
            $searchTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'data' => $posts->items(),
                'search_term' => $searchTerm,
                'total_results' => $posts->total(),
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
                'X-Search-Time' => $searchTime
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'error_code' => 'VALIDATION_ERROR',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error_code' => 'SEARCH_ERROR',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/sitemap",
     *     summary="Get sitemap data",
     *     description="Retrieve sitemap data for all published posts and pages. Useful for SEO and site navigation.",
     *     operationId="getSitemap",
     *     tags={"Utility"},
     *     @OA\Response(
     *         response=200,
     *         description="Sitemap data retrieved successfully",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         @OA\Property(
     *                             property="posts",
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="title", type="string", example="Sample Post"),
     *                                 @OA\Property(property="slug", type="string", example="sample-post"),
     *                                 @OA\Property(property="url", type="string", example="/posts/sample-post"),
     *                                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                                 @OA\Property(property="published_at", type="string", format="date-time")
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="pages",
     *                             type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="title", type="string", example="About Us"),
     *                                 @OA\Property(property="slug", type="string", example="about-us"),
     *                                 @OA\Property(property="url", type="string", example="/pages/about-us"),
     *                                 @OA\Property(property="updated_at", type="string", format="date-time")
     *                             )
     *                         )
     *                     )
     *                 )
     *             }
     *         ),
     *         @OA\Header(
     *             header="Cache-Control",
     *             description="Cache control header",
     *             @OA\Schema(type="string", example="public, max-age=3600")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function sitemap(): JsonResponse
    {
        try {
            $posts = Post::where('status', 'published')
                ->select(['id', 'title', 'slug', 'updated_at', 'published_at'])
                ->orderBy('published_at', 'desc')
                ->get()
                ->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'url' => "/posts/{$post->slug}",
                        'type' => 'post',
                        'updated_at' => $post->updated_at,
                        'published_at' => $post->published_at,
                    ];
                });

            $pages = Page::where('status', 'published')
                ->select(['id', 'title', 'slug', 'updated_at'])
                ->orderBy('title')
                ->get()
                ->map(function ($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'slug' => $page->slug,
                        'url' => "/pages/{$page->slug}",
                        'type' => 'page',
                        'updated_at' => $page->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'posts' => $posts,
                    'pages' => $pages,
                ]
            ], 200, [
                'Cache-Control' => 'public, max-age=3600', // 1 hour cache
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate sitemap',
                'error_code' => 'SITEMAP_ERROR',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
