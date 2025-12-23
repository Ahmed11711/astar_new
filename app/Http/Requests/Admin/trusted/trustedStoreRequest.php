<?php

namespace App\Http\Requests\Admin\trusted;

use App\Http\Requests\BaseRequest\BaseRequest;

class trustedStoreRequest extends BaseRequest
{


    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'count' => 'required|integer',
        ];
    }
}
