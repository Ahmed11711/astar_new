<?php

namespace App\Http\Requests\Admin\Subtopic;
use App\Http\Requests\BaseRequest\BaseRequest;
class SubtopicStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'topic_id' => 'required|integer|exists:topics,id',
            'is_active' => 'required|integer',
        ];
    }
}
