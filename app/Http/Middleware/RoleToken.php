<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class RoleToken
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // جلب الـ token
            $token = JWTAuth::parseToken();
            $claims = $token->getPayload();

            // قراءة البيانات من الـ claims بدل الـ DB
            $userId = $claims->get('user_id');
            $userRole = $claims->get('role'); // ممكن تكون nullable
            $userName = $claims->get('name');
            $userGradeId = $claims->get('grade_id') ?? null; // لو مش موجودة تكون null

            // تمرير البيانات للـ request
            $request->merge([
                'user_id' => $userId,
                'user_role' => $userRole,
                'user_grade' => $userGradeId,
            ]);

            // التحقق من الرول لو موجود في الراوت
            $roles = (array) $request->route()?->getAction('roles');
            if (!empty($roles)) {
                // لو role موجود، وشيك عليه، لو مش موجود في الـ claims اعتبر unauthorized
                if (!$userRole || !in_array($userRole, $roles)) {
                    Log::warning('Unauthorized role access attempt', [
                        'user_role' => $userRole,
                        'allowed' => $roles,
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access'
                    ], 403);
                }
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['success' => false, 'message' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['success' => false, 'message' => 'Token invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'message' => 'Token not provided'], 401);
        } catch (\Throwable $e) {
            Log::critical('Unexpected error in RoleToken middleware', [
                'exception' => $e,
            ]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
