<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Traits\OTPTrait;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Api\Auth\loginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\VerifyEmailRequest;
use App\Http\Requests\Api\Auth\VerifyAffiliateRequest;
use App\Models\userBalance;

class AuthController extends Controller
{
    use ApiResponseTrait,OTPTrait;

    public function login(loginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse('Invalid credentials', 401);
            }
            $user = auth()->user();
            $user->token = $token;

            $user->balance = UserBalance::where('user_id', $user->id)->value('balance') ?? 0;
            return $this->successResponse([
               'user'  => $user,
            ], 'Login successful', 200);
        } catch (JWTException $e) {
            return $this->errorResponse('Could not create token', 500);
        }
    }

    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['affiliate_code'] = $this->generateAffiliateCode();
        $validatedData['otp'] = $this->generateOtp(6);
        $user = User::create($validatedData);
        $token = JWTAuth::fromUser($user);

        return $this->successResponse([
            'token' => $token,
            'user'  => $user,
        ], 'User registered successfully', 201);
    }

    private function generateAffiliateCode()
    {
        do {
            $code = 'AFF-' . strtoupper(Str::random(6));
        } while (User::where('affiliate_code', $code)->exists());
        return $code;
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->successResponse([], 'User logged out successfully', 200);
        } catch (JWTException $e) {
            return $this->errorResponse('Could not invalidate token', 500);
        }
    }

    public function me(Request $request)
    {
        $user = auth()->user();
        $user->balance = UserBalance::where('user_id', $user->id)->value('balance') ?? 0;

        return $this->successResponse([
            'user' => $user,
        ], 'User details fetched successfully', 200);
    }
    public function verifyEmail(VerifyEmailRequest $request)
    {
        $validatedData = $request->validated();

        $email = $validatedData['email'];
        $otp   = $validatedData['otp'];

        $user = User::where('email', $email)->first();

        if (!$user || $user->otp !== $otp) {
            return $this->errorResponse('Invalid OTP', 400);
        }

        $user->update([
            'email_verified_at' => now(),
            'is_verified' => true,
            'otp' => null,
        ]);

        return $this->successResponse([], 'Email verified successfully', 200);
    }

  public function verifyAffiliate(VerifyAffiliateRequest $request)
{
    $validatedData = $request->validated();
    $code = $validatedData['affiliate_code'];

    $affiliateUser = User::where('affiliate_code', $code)->first();

    if (!$affiliateUser) {
        return $this->errorResponse('Affiliate code is invalid', 404);
    }

     $balance = UserBalance::firstOrCreate(
        ['user_id' => $affiliateUser->id],
        ['balance' => 0, ]
    );

     $balance->increment('balance', 10);

    return $this->successResponse([], 'Affiliate code is valid, balance updated', 200);
}
}