<?php

namespace App\Http\Requests\Admin\grade;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Validation\Rule;

class gradeUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('grades')->ignore($this->route('grade')),
            ],
            'order' => 'nullable|string|max:255',
        ];
    }
}
