<?php

namespace App\Http\Controllers\Auth\OTP;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Otp\SendOtpRequest;
use App\Mail\SendEmailRegister;
use App\Models\UserOtp;
use App\Traits\OTPTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class sendOtpController extends Controller
{
    use OTPTrait;
    public function sendOtp(SendOtpRequest $request)
    {
        // Logic to send OTP
        $email = $request->input('email');
        $otp = $this->generateOtp(6);
        $this->storeOtp($email, $otp);
        // Send email with OTP
        Mail::send(new SendEmailRegister($email, $otp));
    }

    public function checkOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string',
        ]);

        $email = $request->input('email');
        $otpCode = $request->input('otp_code');

        $userOtp = UserOtp::where('email', $email)
            ->where('otp_code', $otpCode)
            ->where('expires_at', '>', now())
            ->orderBy('id', 'desc')
            ->first();

        if ($userOtp) {
            return response()->json(['message' => 'OTP is valid.'], 200);
        } else {
            return response()->json(['message' => 'OTP is invalid or expired.'], 400);
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
