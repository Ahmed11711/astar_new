<?php

namespace App\Http\Requests\Admin\Report;
use App\Http\Requests\BaseRequest\BaseRequest;
class ReportUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => 'sometimes|required|integer',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'type' => 'sometimes|required|in:admin,school',
            'response' => 'nullable|sometimes|string',
            'status' => 'sometimes|required|in:pending,reviewed,resolved',
        ];
    }
}
