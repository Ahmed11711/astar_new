<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class CheckJwtToken
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();

            $request->merge([
                'auth_user_id' => $payload->get('user_id'),
                'auth_email'   => $payload->get('email'),
                'auth_role'    => $payload->get('role'),
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json(['success' => false, 'message' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['success' => false, 'message' => 'Token invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'message' => 'Token not provided'], 401);
        }

        return $next($request);
    }
}
