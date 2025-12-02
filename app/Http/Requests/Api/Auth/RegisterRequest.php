<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\BaseRequest\BaseRequest;
 
class RegisterRequest extends BaseRequest
{
    
 
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            
        ];
    }
}
