<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Traits\ApiResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
 use ApiResponseTrait;

 public function login(LoginRequest $request)
 {
  $credentials = $request->only('email', 'password');

  try {
   // استخدام guard api عند محاولة الـ attempt
   if (!$token = auth()->guard('api')->attempt($credentials)) {
    return $this->errorResponse('Invalid credentials', 401);
   }

   $user = auth()->guard('api')->user();

   $user->access_token  = $token;
   $user->refresh_token = auth()->guard('api')->refresh($token);

   return $this->successResponse(new LoginResource($user));
  } catch (JWTException $e) {
   \Log::error('JWT error: ' . $e->getMessage());
   return $this->errorResponse('Could not create token', 500);
  } catch (\Exception $e) {
   \Log::error('General error: ' . $e->getMessage());
   return $this->errorResponse('An unexpected error occurred', 500);
  }
 }
}
