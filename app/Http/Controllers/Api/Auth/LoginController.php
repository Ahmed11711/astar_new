<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\LoginResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;





class LoginController extends Controller
{

 use ApiResponseTrait;


 public function login(Request $request)
 {
  $credentials = $request->only('email', 'password');

  if (! $token = JWTAuth::attempt($credentials)) {
   return $this->errorResponse('Invalid credentials', 401);
  }

  $user = JWTAuth::user();
  $user->access_token = $token;
  $user->refresh_token = null;

  return $this->successResponse(
   new LoginResource($user),
   'Login Successfully'
  );
 }
}
