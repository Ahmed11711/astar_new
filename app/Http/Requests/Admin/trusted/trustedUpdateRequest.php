<?php

namespace App\Http\Requests\Admin\trusted;

use App\Http\Requests\BaseRequest\BaseRequest;

class trustedUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'count' => 'sometimes|required|integer',
        ];
    }
}
