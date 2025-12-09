<?php

namespace App\Http\Requests\Admin\school;

use App\Http\Requests\BaseRequest\BaseRequest;

class schoolStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'contact_email' => 'required|string|max:255',
            'is_active' => 'nullable|integer',
            'meta' => 'nullable|array',
        ];
    }
}
