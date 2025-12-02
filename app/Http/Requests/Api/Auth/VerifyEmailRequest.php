<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends BaseRequest
{
    

    
    public function rules(): array
    {
        return [
             'email' => 'required|email|exists:users,email',
             'otp'   => 'required|string|min:6',
        ];
    }
}
