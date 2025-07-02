<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ResourcePermissionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Super admin has access to everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Auto-detect permission from route
        $permission = $this->detectPermissionFromRoute($request);
        
        if ($permission && !$user->hasPermission($permission)) {
            return redirect()->route('unauthorized')
                ->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
    
    private function detectPermissionFromRoute(Request $request): ?string
    {
        $route = $request->route();
        $routeName = $route->getName();
        
        // Extract resource and action from route name (e.g., 'posts.create' -> 'posts.create')
        if ($routeName && Str::contains($routeName, '.')) {
            // Skip dashboard and other non-resource routes
            if (Str::startsWith($routeName, ['dashboard', 'profile', 'unauthorized'])) {
                return null;
            }
            return $routeName;
        }
        
        // Fallback: extract from URL path
        $segments = $request->segments();
        if (count($segments) >= 2 && $segments[0] === 'admin') {
            $resource = $segments[1];
            
            // Skip dashboard and other non-resource routes
            if (in_array($resource, ['dashboard', 'profile'])) {
                return null;
            }
            
            $action = $this->detectActionFromSegments($segments);
            return "{$resource}.{$action}";
        }
        
        return null;
    }
    
    private function detectActionFromSegments(array $segments): string
    {
        // Check for 'create' in URL segments
        if (in_array('create', $segments)) {
            return 'create';
        }
        
        // Check for 'edit' in URL segments
        if (in_array('edit', $segments)) {
            return 'edit';
        }
        
        // Check if there's a resource ID (indicating a show/edit action)
        if (count($segments) >= 3 && is_numeric($segments[2])) {
            return 'edit'; // Assume edit for now, can be refined
        }
        
        // Default to 'view' for index pages
        return 'view';
    }
}