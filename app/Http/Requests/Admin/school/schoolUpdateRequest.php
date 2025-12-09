<?php

namespace App\Http\Requests\Admin\school;
use App\Http\Requests\BaseRequest\BaseRequest;
class schoolUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|sometimes|string|max:255',
            'contact_email' => 'sometimes|required|string|max:255',
            'is_active' => 'sometimes|required|integer',
            'meta' => 'nullable|sometimes|array',
        ];
    }
}
