<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Page;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @OA\Info(
 *     title="Headless CMS API",
 *     version="1.0.0",
 *     description="A headless CMS API for managing posts, pages, and categories",
 *     @OA\Contact(
 *         email="work.rahmathidayat@gmail.com"
 *     )
 * )
 * @OA\Server(
 *     url="/api/v1",
 *     description="API Version 1"
 * )
 *
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     title="Post",
 *     description="Post model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Sample Post Title"),
 *     @OA\Property(property="slug", type="string", example="sample-post-title"),
 *     @OA\Property(property="excerpt", type="string", example="This is a sample excerpt"),
 *     @OA\Property(property="content", type="string", example="This is the full content of the post"),
 *     @OA\Property(property="status", type="string", example="published"),
 *     @OA\Property(property="featured_image", type="string", nullable=true, example="/images/featured.jpg"),
 *     @OA\Property(property="meta_title", type="string", nullable=true, example="SEO Title"),
 *     @OA\Property(property="meta_description", type="string", nullable=true, example="SEO Description"),
 *     @OA\Property(property="author_name", type="string", nullable=true, example="John Doe"),
 *     @OA\Property(property="published_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T14:00:00Z"),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Category")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     description="Category model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Technology"),
 *     @OA\Property(property="slug", type="string", example="technology"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Technology related posts"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T14:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="PaginationMeta",
 *     type="object",
 *     title="Pagination Meta",
 *     description="Pagination metadata",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=10),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=150)
 * )
 *
 * @OA\Schema(
 *     schema="SitemapItem",
 *     type="object",
 *     title="Sitemap Item",
 *     description="Sitemap item for posts and pages",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Sample Title"),
 *     @OA\Property(property="slug", type="string", example="sample-title"),
 *     @OA\Property(property="url", type="string", example="/posts/sample-title"),
 *     @OA\Property(property="type", type="string", example="post"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T14:00:00Z"),
 *     @OA\Property(property="published_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Error Response",
 *     description="Standard error response",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="An error occurred"),
 *     @OA\Property(property="error", type="string", nullable=true, example="Detailed error message")
 * )
 *
 * @OA\Schema(
 *     schema="NotFoundResponse",
 *     type="object",
 *     title="Not Found Response",
 *     description="Resource not found response",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Resource not found")
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     title="Validation Error Response",
 *     description="Validation error response",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Validation failed")
 * )
 */
class PostController extends Controller
{
    /**
     * @OA\Get(
 *     path="/posts",
 *     summary="Get all published posts",
 *     description="Retrieve a paginated list of all published posts with their categories",
 *     operationId="getPosts",
 *     tags={"Posts"},
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Number of posts per page (max 100)",
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
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Post")
 *             ),
 *             @OA\Property(
 *                 property="meta",
 *                 ref="#/components/schemas/PaginationMeta"
 *             )
 *         )
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
     * @OA\Get(
 *     path="/posts/{post}",
 *     summary="Get a specific post",
 *     description="Retrieve a specific published post by ID with its categories",
 *     operationId="getPost",
 *     tags={"Posts"},
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         description="Post ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/Post"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Post not found",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
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
     * @OA\Get(
 *     path="/posts/search",
 *     summary="Search posts",
 *     description="Search for published posts by title, content, or excerpt",
 *     operationId="searchPosts",
 *     tags={"Posts"},
 *     @OA\Parameter(
 *         name="q",
 *         in="query",
 *         description="Search query",
 *         required=true,
 *         @OA\Schema(type="string", minLength=1)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Number of posts per page (max 100)",
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
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Post")
 *             ),
 *             @OA\Property(property="search_term", type="string"),
 *             @OA\Property(
 *                 property="meta",
 *                 ref="#/components/schemas/PaginationMeta"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request - search term required",
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
     * @OA\Get(
 *     path="/sitemap",
 *     summary="Get sitemap data",
 *     description="Retrieve sitemap data for all published posts and pages",
 *     operationId="getSitemap",
 *     tags={"Utility"},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="posts",
 *                     type="array",
 *                     @OA\Items(ref="#/components/schemas/SitemapItem")
 *                 ),
 *                 @OA\Property(
 *                     property="pages",
 *                     type="array",
 *                     @OA\Items(ref="#/components/schemas/SitemapItem")
 *                 )
 *             ),
 *             @OA\Property(property="generated_at", type="string", format="date-time")
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
            // Get published posts
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
                        'updated_at' => $post->updated_at->toISOString(),
                        'published_at' => $post->published_at->toISOString(),
                        'type' => 'post'
                    ];
                });

            // Get published pages
            $pages = Page::where('status', 'published')
                ->select(['id', 'title', 'slug', 'updated_at', 'published_at'])
                ->orderBy('published_at', 'desc')
                ->get()
                ->map(function ($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'slug' => $page->slug,
                        'url' => "/pages/{$page->slug}",
                        'updated_at' => $page->updated_at->toISOString(),
                        'published_at' => $page->published_at->toISOString(),
                        'type' => 'page'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'posts' => $posts,
                    'pages' => $pages
                ],
                'generated_at' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate sitemap',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    // Note: store, update, destroy methods are not implemented
    // as this is a read-only API for headless CMS consumption
}
