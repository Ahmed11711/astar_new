<?php

namespace App\Http\Requests\Admin\Topic;
use App\Http\Requests\BaseRequest\BaseRequest;
class TopicUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'subject_id' => 'sometimes|required|integer|exists:subjects,id',
            'is_active' => 'sometimes|required|integer',
        ];
    }
}
