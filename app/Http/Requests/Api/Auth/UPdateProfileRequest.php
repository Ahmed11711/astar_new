<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest\BaseRequest;

class UPdateProfileRequest extends BaseRequest
{



    public function rules(): array
    {
        $userId = $this->route('user') ?? optional($this->user())->id;

        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => 'sometimes|string|min:6',
            'phone'=>'sometimes|string|max:20',
            'address'=>'sometimes|string|max:500',
            'profile_image'=>'sometimes|file|max:2048',
        ];
    }
}
