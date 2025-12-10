<?php

namespace App\Http\Requests\Admin\successStories;
use App\Http\Requests\BaseRequest\BaseRequest;
class successStoriesStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'title' => 'required|string|max:255',
            'img' => 'required|string|max:255|file|max:2048',
        ];
    }
}
