<?php

namespace App\Http\Requests\Admin\grade;

use App\Http\Requests\BaseRequest\BaseRequest;

class gradeStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:grades,name',
            'order' => 'nullable|string|max:255',
        ];
    }
}
