<?php

namespace App\Http\Requests\Admin\Subtopic;
use App\Http\Requests\BaseRequest\BaseRequest;
class SubtopicUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'topic_id' => 'sometimes|required|integer|exists:topics,id',
            'is_active' => 'sometimes|required|integer',
        ];
    }
}
