<?php

namespace App\Http\Requests\Admin\setting;
use App\Http\Requests\BaseRequest\BaseRequest;
class settingUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'sometimes|required|string|max:255|unique:settings,key,'.$this->route('setting').',id',
            'value' => 'nullable|sometimes|string',
        ];
    }
}
