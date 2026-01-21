<?php

namespace App\Http\Requests\Admin\bugs;
use App\Http\Requests\BaseRequest\BaseRequest;
class bugsStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'question' => 'required|string',
            'answer' => 'nullable|string',
        ];
    }
}
