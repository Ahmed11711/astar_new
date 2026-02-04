<?php

namespace App\Http\Controllers\Auth\OTP;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Otp\checkOtpRequest;
use App\Http\Requests\Auth\Otp\SendOtpRequest;
use App\Mail\SendEmailRegister;
use App\Models\UserOtp;
use App\Traits\ApiResponseTrait;
use App\Traits\OTPTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class sendOtpController extends Controller
{
    use OTPTrait, ApiResponseTrait;
    public function sendOtp(SendOtpRequest $request)
    {
        $email = $request->input('email');

        $otp = $this->generateOtp(6);
        $this->storeOtp($email, $otp);

        Mail::to($email)->send(new SendEmailRegister($email, $otp));
        return $this->successResponse(null, 'OTP sent successfully.');
    }


    public function checkOtp(checkOtpRequest $request)
    {
        $request->validated();

        $email = $request->input('email');
        $otpCode = $request->input('otp_code');

        $userOtp = UserOtp::where('email', $email)
            ->where('otp_code', $otpCode)
            ->where('expires_at', '>', now())
            ->orderBy('id', 'desc')
            ->first();

        if ($userOtp) {
            return  $this->successResponse(null, 'OTP is valid.');
        } else {
            return $this->errorResponse('OTP is invalid or expired.', 400);
        }
    }


    private function storeOtp($email, $otp)
    {

        UserOtp::create([
            'email' => $email,
            'otp_code' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);
    }
}
