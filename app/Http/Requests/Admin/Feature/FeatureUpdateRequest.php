<?php

namespace App\Http\Requests\Admin\Feature;
use App\Http\Requests\BaseRequest\BaseRequest;
class FeatureUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'sometimes|required|string|max:255|unique:features,key,'.$this->route('feature').',id',
            'label' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:boolean,number,text',
        ];
    }
}
