<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     title="Login Request",
 *     description="User login credentials",
 *     required={"email", "password"},
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="User's email address",
 *         example="admin@example.com"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         description="User's password (minimum 8 characters)",
 *         minLength=8,
 *         example="password123"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="LoginResponse",
 *     type="object",
 *     title="Login Response",
 *     description="Successful authentication response with token",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         description="Indicates if the request was successful",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Human-readable success message",
 *         example="Login successful"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         description="Authentication data",
 *         @OA\Property(
 *             property="token",
 *             type="string",
 *             description="Bearer token for API authentication",
 *             example="1|abc123def456ghi789jkl012mno345pqr678stu901vwx234yz"
 *         ),
 *         @OA\Property(
 *             property="token_type",
 *             type="string",
 *             description="Type of the authentication token",
 *             example="Bearer"
 *         ),
 *         @OA\Property(
 *             property="user",
 *             type="object",
 *             description="Authenticated user information",
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 description="Unique user identifier",
 *                 example=1
 *             ),
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 description="User's full name",
 *                 example="John Doe"
 *             ),
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 format="email",
 *                 description="User's email address",
 *                 example="john.doe@example.com"
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="LogoutResponse",
 *     type="object",
 *     title="Logout Response",
 *     description="Successful logout confirmation",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         description="Indicates if logout was successful",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Logout confirmation message",
 *         example="Logout successful"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UnauthorizedResponse",
 *     type="object",
 *     title="Unauthorized Response",
 *     description="Authentication failure response",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         description="Always false for error responses",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Error message explaining the authentication failure",
 *         example="Invalid credentials or token expired"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     title="Validation Error Response",
 *     description="Request validation failure response",
 *     @OA\Property(
 *         property="success",
 *         type="boolean",
 *         description="Always false for error responses",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="General validation error message",
 *         example="Validation failed"
 *     ),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         description="Field-specific validation errors",
 *         additionalProperties={
 *             "type": "array",
 *             "items": {"type": "string"}
 *         },
 *         example={
 *             "email": {"The email field is required."},
 *             "password": {"The password must be at least 8 characters."}
 *         }
 *     )
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Authenticate user",
     *     description="Authenticate a user with email and password, returning an access token for API requests. The token should be included in the Authorization header as 'Bearer {token}' for subsequent requests.",
     *     operationId="authenticateUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User login credentials",
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Authentication successful",
     *         @OA\JsonContent(ref="#/components/schemas/LoginResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials provided",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors in request data",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many login attempts",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Too many attempts. Please try again later.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Login failed"),
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Revoke all existing tokens for this user
            $user->tokens()->delete();

            // Create new token
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="Logout user",
     *     description="Revoke the current access token, effectively logging out the user. The token will no longer be valid for API requests.",
     *     operationId="logoutUser",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(ref="#/components/schemas/LogoutResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token invalid or expired",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Logout failed"),
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke the current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/auth/user",
     *     summary="Get authenticated user profile",
     *     description="Retrieve the profile information of the currently authenticated user. This endpoint requires a valid bearer token.",
     *     operationId="getCurrentUser",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="User profile information",
     *                 @OA\Property(property="id", type="integer", description="User ID", example=1),
     *                 @OA\Property(property="name", type="string", description="Full name", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", description="Email address", example="john.doe@example.com"),
     *                 @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, description="Email verification timestamp", example="2024-01-01T12:00:00Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Account creation timestamp", example="2024-01-01T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Last profile update timestamp", example="2024-01-01T14:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication required",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     )
     * )
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }
}
