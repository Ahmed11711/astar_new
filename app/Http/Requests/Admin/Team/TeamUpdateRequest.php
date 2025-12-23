<?php

namespace App\Http\Requests\Admin\Team;

use App\Http\Requests\BaseRequest\BaseRequest;

class TeamUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|sometimes|string|max:255',
            'img' => 'nullable|file',
            'is_active' => 'sometimes|required|integer',
        ];
    }
}
