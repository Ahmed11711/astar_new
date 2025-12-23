<?php

namespace App\Http\Requests\Admin\Team;

use App\Http\Requests\BaseRequest\BaseRequest;

class TeamStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'img' => 'nullable|file',
            'is_active' => 'nullable|integer',
        ];
    }
}
