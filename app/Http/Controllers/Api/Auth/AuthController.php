<?php

namespace App\Http\Controllers\Api\Auth;

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
use App\Models\withdraw;

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
            $user->fcm_token = $request->fcm_token;
            $user->save();
            $user->token = $token;

            $userBalance = UserBalance::where('user_id', $user->id)->first();
            $user->balance = $userBalance->balance ?? 0;
            $user->balance_affiliate = $userBalance->affiliate_balance ?? 0;
            $user->myLink = config('app.url') . '/' . $user->affiliate_code;
            $user->count=User::where('referred_by',$user->affiliate_code)->count() ?? 0;
            $user->count_withdraw_pending=withdraw::where('user_id',$user->id)->where('status','pending')->count() ?? 0;


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
         $userBalance = UserBalance::where('user_id', $user->id)->first();
            $user->balance = $userBalance->balance ?? 0;
            $user->balance_affiliate = $userBalance->affiliate_balance ?? 0;
            $user->myLink = config('app.url') . '/' . $user->affiliate_code;
            $user->count=User::where('referred_by',$user->affiliate_code)->count() ?? 0;
            $user->count_withdraw_pending=withdraw::where('user_id',$user->id)->where('status','pending')->count() ?? 0;


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
            ['balance' => 0,]
        );

        $balance->increment('balance', 10);
        $user=User::where('email',$validatedData['email'])->first();
        $user->referred_by=$affiliateUser->affiliate_code;
        $user->save();

        return $this->successResponse([], 'Affiliate code is valid, balance updated', 200);
    }

    public function updateProfile(UPdateProfileRequest $request)
    {
        $user = auth()->user();

        $validatedData = $request->validated();

        if($request->hasFile('profile_image')){
            $file = $request->file('profile_image');
            $path = $file->store('profile_images', 'public');
            $validatedData['profile_image'] = $path;
        }

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        $user->update($validatedData);
        return $this->successResponse([
            'user' => $user,
        ], 'Profile updated successfully', 200);
    }

    public function resendOtp(resendOtpRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        $otp = $this->generateOtp(6);
        $user->update(['otp' => $otp]);

        // Here you would typically send the OTP via email or SMS

        return $this->successResponse([$otp], 'OTP resent successfully', 200);
    }
}
