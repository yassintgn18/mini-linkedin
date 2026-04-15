<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!$user = auth('api')->user()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Check if user's role is allowed
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Forbidden - You do not have access to this resource'
            ], 403);
        }

        return $next($request);
    }
}