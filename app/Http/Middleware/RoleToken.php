<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class RoleToken
{
   public function handle(Request $request, Closure $next)
{
    try {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // roles جاية من الروت
        $roles = $request->route()?->defaults['roles'] ?? [];

        if (!empty($roles) && !in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }

    return $next($request);
}

}