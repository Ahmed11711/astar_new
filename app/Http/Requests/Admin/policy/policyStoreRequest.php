<?php

namespace App\Http\Requests\Admin\policy;
use App\Http\Requests\BaseRequest\BaseRequest;
class policyStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
