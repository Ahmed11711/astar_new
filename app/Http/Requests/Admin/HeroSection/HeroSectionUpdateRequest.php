<?php

namespace App\Http\Requests\Admin\HeroSection;

use App\Http\Requests\BaseRequest\BaseRequest;

class HeroSectionUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'nullable|sometimes|string',
            'background_image' => 'nullable|sometimes|file',
        ];
    }
}
