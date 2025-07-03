<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\Permission;

class ResourcePermissionMiddleware
{
    /**
     * Routes that don't require specific permissions
     */
    private array $publicRoutes = [
        'dashboard',
        'profile',
        'unauthorized'
    ];

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated.');
        }
        
        // Super admin has access to everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has any active roles
        if (!$this->userHasActiveRoles($user)) {
            return redirect()->route('unauthorized')
                ->with('error', 'You do not have any assigned roles or your roles are inactive.');
        }

        // Get the current route name
        $routeName = $request->route()->getName();
        
        // Allow access to public routes
        if (in_array($routeName, $this->publicRoutes)) {
            return $next($request);
        }

        // Get required permission for this route
        $requiredPermission = $this->getRequiredPermission($routeName);
        
        if ($requiredPermission) {
            if (!$user->hasPermission($requiredPermission)) {
                return redirect()->route('unauthorized')
                    ->with('error', "You do not have permission to access this resource. Required permission: {$requiredPermission}");
            }
        }
        // If we can't determine the permission and it's not a public route, deny access
        elseif ($routeName && !Str::startsWith($routeName, ['dashboard', 'profile'])) {
            return redirect()->route('unauthorized')
                ->with('error', 'Access to this resource requires specific permissions that could not be determined.');
        }

        return $next($request);
    }
    
    /**
     * Get required permission for a route by checking the database
     */
    private function getRequiredPermission(string $routeName): ?string
    {
        // Cache permissions for better performance
        $permissions = Cache::remember('active_permissions', 3600, function () {
            return Permission::where('is_active', true)
                ->pluck('name')
                ->toArray();
        });

        // Direct match - if route name exactly matches a permission
        if (in_array($routeName, $permissions)) {
            return $routeName;
        }

        // Pattern matching for common route patterns
        $permissionPatterns = $this->getPermissionPatterns($permissions);
        
        foreach ($permissionPatterns as $pattern => $permission) {
            if (Str::is($pattern, $routeName)) {
                return $permission;
            }
        }

        return null;
    }
    
    /**
     * Generate permission patterns from database permissions
     */
    private function getPermissionPatterns(array $permissions): array
    {
        $patterns = [];
        
        foreach ($permissions as $permission) {
            // Handle standard CRUD patterns
            if (Str::contains($permission, '.')) {
                [$resource, $action] = explode('.', $permission, 2);
                
                switch ($action) {
                    case 'view':
                        $patterns["{$resource}.index"] = $permission;
                        $patterns["{$resource}.show"] = $permission;
                        break;
                    case 'create':
                        $patterns["{$resource}.create"] = $permission;
                        $patterns["{$resource}.store"] = $permission;
                        break;
                    case 'edit':
                        $patterns["{$resource}.edit"] = $permission;
                        $patterns["{$resource}.update"] = $permission;
                        break;
                    case 'delete':
                        $patterns["{$resource}.destroy"] = $permission;
                        break;
                }
            }
        }
        
        return $patterns;
    }
    
    /**
     * Check if user has any active roles
     */
    private function userHasActiveRoles($user): bool
    {
        return $user->roles()
            ->where('is_active', true)
            ->exists();
    }
    
    /**
     * Clear permissions cache (useful when permissions are updated)
     */
    public static function clearPermissionsCache(): void
    {
        Cache::forget('active_permissions');
    }
}