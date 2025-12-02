<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\User;
use App\Traits\OTPTrait;
use App\Models\userBalance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Api\Auth\loginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\resendOtpRequest;
use App\Http\Requests\Api\Auth\VerifyEmailRequest;
use App\Http\Requests\Api\Auth\UPdateProfileRequest;
use App\Http\Requests\Api\Auth\VerifyAffiliateRequest;

class AuthController extends Controller
{
    use ApiResponseTrait, OTPTrait;

    public function login(loginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse('Invalid credentials', 401);
            }
            $user = auth()->user();

            if($user->role!='admin' || $user->role=='super_admin'){
                return $this->errorResponse('Unauthorized access', 403);
            }

            $user->token = $token;

            $user->balance = UserBalance::where('user_id', $user->id)->value('balance') ?? 0;
            return $this->successResponse([
                'user'  => $user,
            ], 'Login successful', 200);
        } catch (JWTException $e) {
            return $this->errorResponse('Could not create token', 500);
        }
    }

}