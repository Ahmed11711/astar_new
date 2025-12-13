<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;


class LoginController extends Controller
{
 use ApiResponseTrait;

 public function login(Request $request)
 {
  $credentials = $request->only('email', 'password');

  try {
   if (! $token = JWTAuth::attempt($credentials)) {
    return response()->json(['error' => 'Invalid credentials'], 401);
   }

   return $token;

   // Get the authenticated user.
   $user = auth()->user();

   // (optional) Attach the role to the token.
   $token = JWTAuth::claims(['role' => $user->role])->fromUser($user);

   return response()->json(compact('token'));
  } catch (JWTException $e) {
   return response()->json(['error' => 'Could not create token'], 500);
  }
 }
}
