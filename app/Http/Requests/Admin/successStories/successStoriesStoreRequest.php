<?php

namespace App\Http\Requests\Admin\successStories;

use App\Http\Requests\BaseRequest\BaseRequest;

class successStoriesStoreRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'title' => 'required|string|max:255',
            'info' => 'required|string',
        ];
    }
}
