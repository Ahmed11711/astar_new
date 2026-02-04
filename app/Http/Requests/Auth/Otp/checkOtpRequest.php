<?php

namespace App\Http\Requests\Auth\Otp;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class checkOtpRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'otp_code' => 'required|string',
        ];
    }
}
