<?php

namespace App\Http\Requests\Admin\Topic;
use App\Http\Requests\BaseRequest\BaseRequest;
class TopicStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'subject_id' => 'required|integer|exists:subjects,id',
            'is_active' => 'required|integer',
        ];
    }
}
