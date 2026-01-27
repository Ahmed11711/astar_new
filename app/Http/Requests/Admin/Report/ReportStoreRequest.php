<?php

namespace App\Http\Requests\Admin\Report;
use App\Http\Requests\BaseRequest\BaseRequest;
class ReportStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:admin,school',
            'response' => 'nullable|string',
            'status' => 'required|in:pending,reviewed,resolved',
        ];
    }
}
