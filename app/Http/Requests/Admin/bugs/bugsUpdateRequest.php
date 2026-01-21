<?php

namespace App\Http\Requests\Admin\bugs;
use App\Http\Requests\BaseRequest\BaseRequest;
class bugsUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|required|integer|exists:users,id',
            'question' => 'sometimes|required|string',
            'answer' => 'nullable|sometimes|string',
        ];
    }
}
