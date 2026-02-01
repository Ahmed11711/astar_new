<?php

namespace App\Http\Requests\Admin\paper;

use App\Http\Requests\BaseRequest\BaseRequest;

class paperStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'type' => 'nullable|string',
            'grade_id' => 'required|integer|exists:grades,id',
            'subject_id' => 'required|integer|exists:subjects,id',
        ];
    }
}
