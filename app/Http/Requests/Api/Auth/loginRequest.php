<?php

namespace App\Http\Requests\Api\Auth;

 use App\Http\Requests\BaseRequest\BaseRequest;

class loginRequest extends BaseRequest
{
   
   
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
        ];
    }
}
