<?php

namespace App\Http\Controllers\Auth\ForgetPassword;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RestPasswordRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    use ApiResponseTrait;
    public function reset(RestPasswordRequest $request)
    {
        $request->validated();
        $token = $request->token;

        $hashedToken = Hash::make($token);
        Log::alert("ssss", [$hashedToken]);

        $tokenData = DB::table('password_reset_tokens')->where('token', $hashedToken)->first();

        if (!$tokenData) {
            return $this->errorResponse('Invalid or expired token');
        }
        $user = User::where('email', $tokenData->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        return response()->json([
            'message' => 'Password reset successfully!',
            'status' => 'success',
        ]);
    }
}
