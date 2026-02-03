<?php

namespace App\Http\Requests\Admin\HeroSection;

use App\Http\Requests\BaseRequest\BaseRequest;

class HeroSectionStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string',
            'background_image' => 'nullable|file',
        ];
    }
}
