<?php

namespace App\Http\Requests\Admin\Blog;

use App\Http\Requests\BaseRequest\BaseRequest;

class BlogUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:blogs,slug,' . $this->route('blog') . ',id',
            'content' => 'sometimes|required|string',
            'img' => 'nullable|file',
            'author_id' => 'nullable|sometimes|integer',
            'is_published' => 'sometimes|required|integer',
        ];
    }
}
