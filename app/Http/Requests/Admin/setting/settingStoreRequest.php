<?php

namespace App\Http\Requests\Admin\setting;
use App\Http\Requests\BaseRequest\BaseRequest;
class settingStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'nullable|string',
        ];
    }
}
