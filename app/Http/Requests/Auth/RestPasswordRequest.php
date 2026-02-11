<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class RestPasswordRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];
    }
}
