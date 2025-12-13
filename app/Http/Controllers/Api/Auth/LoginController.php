<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Traits\ApiResponseTrait;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
 use ApiResponseTrait;

 public function login(LoginRequest $request)
 {
  $credentials = $request->only('email', 'password');

  try {
   // سجل بيانات الدخول المحاولة
   Log::info('Login attempt for email: ' . $request->email);

   if (!$token = JWTAuth::attempt($credentials)) {
    Log::warning('Invalid credentials for email: ' . $request->email);
    return $this->errorResponse('Invalid credentials', 401);
   }

   $user = auth()->user();

   if (!$user) {
    Log::error('Authenticated user not found after JWT attempt.');
    return $this->errorResponse('User not found', 500);
   }

   try {
    $user->access_token  = $token;
    $user->refresh_token = JWTAuth::refresh($token);
   } catch (JWTException $e) {
    Log::error('JWT refresh error: ' . $e->getMessage());
    return $this->errorResponse('Could not create refresh token', 500);
   }

   Log::info('Login successful for email: ' . $request->email);
   return $this->successResponse(new LoginResource($user));
  } catch (JWTException $e) {
   Log::error('JWT general error: ' . $e->getMessage());
   return $this->errorResponse('Could not create token', 500);
  } catch (\Exception $e) {
   Log::error('General error: ' . $e->getMessage());
   return $this->errorResponse('An unexpected error occurred', 500);
  }
 }
}
