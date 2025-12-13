<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Traits\ApiResponseTrait;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
 use ApiResponseTrait;

 public function login(LoginRequest $request)
 {
  $credentials = $request->only('email', 'password');

  try {
   if (!$token = JWTAuth::attempt($credentials)) {
    return $this->errorResponse('Invalid credentials', 401);
   }

   $user = auth()->user();

   $user->access_token  = $token;
   $user->refresh_token = JWTAuth::refresh($token);

   return $this->successResponse(new LoginResource($user));
  } catch (JWTException $e) {
   return $this->errorResponse('Could not create token', 500);
  }
 }
}
