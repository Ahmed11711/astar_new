<?php

namespace App\Http\Requests\School;

use App\Http\Requests\BaseRequest\BaseRequest;

class UpdateReportSchoolRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,reviewed,resolved',
            'response' => 'nullable|string',
        ];
    }
}
