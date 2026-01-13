<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleToken
{


    public function handle(Request $request, Closure $next)
    {
        try {
            $token   = JWTAuth::parseToken();
            $claims  = $token->getPayload();

            $userId  = $claims->get('user_id');
            $role    = $claims->get('role');
            $name    = $claims->get('name');
            $email   = $claims->get('email');

            // الأساسيات
            $request->merge([
                'user_id'   => $userId,
                'user_role' => $role,
                'user_name' => $name,
                'user_email' => $email,
            ]);


            if ($role === 'student') {
                $user = User::with('subjects:id')->findOrFail($userId);

                $request->merge([
                    'student_grade_id'    => $user->grades->first()?->id,
                    'student_subject_ids' => $user->subjects->pluck('id')->all(),
                ]);
            }

            $roles = (array) $request->route()?->getAction('roles');
            if (!empty($roles) && !in_array($role, $roles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
        } catch (TokenExpiredException) {
            return response()->json(['success' => false, 'message' => 'Token expired'], 401);
        } catch (TokenInvalidException) {
            return response()->json(['success' => false, 'message' => 'Token invalid'], 401);
        } catch (JWTException) {
            return response()->json(['success' => false, 'message' => 'Token not provided'], 401);
        }

        return $next($request);
    }
}
