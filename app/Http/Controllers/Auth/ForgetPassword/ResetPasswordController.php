<?php

namespace App\Http\Controllers\Auth\ForgetPassword;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RestPasswordRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $email = $request->email;

        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$tokenData || Carbon::parse($tokenData->created_at)->addMinutes(5)->isPast()) {
            return $this->errorResponse('Invalid or expired token');
        }

        if (!Hash::check($token, $tokenData->token)) {
            return $this->errorResponse('Invalid token');
        }

        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return response()->json([
            'message' => 'Password reset successfully!',
            'status' => 'success',
        ]);
    }
}
