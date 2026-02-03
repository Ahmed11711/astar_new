<?php

namespace App\Http\Controllers\Auth\ForgetPassword;

use \App\Models\User;
use App\Http\Controllers\Controller;
use App\Mail\SentOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

class ForgetPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $email = $request->email;

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        $token = Password::createToken($user);

        $resetUrl = 'https://astar.click/' . "/reset-password?token=$token&email=" . urlencode($user->email);

        Mail::to($user->email)->send(new SentOtpMail($resetUrl));

        return response()->json([
            'message' => 'Reset link sent successfully!',
            'status' => 'success',
        ]);
    }
}
