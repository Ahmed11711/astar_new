<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class VerifyAffiliateRequest extends BaseRequest
{
   
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'affiliate_code' => 'required|string',
            'email' => 'required|email|exists:users,email',
        ];
    }
}
