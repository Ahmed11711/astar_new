<?php

namespace App\Http\Requests\Admin\Feature;
use App\Http\Requests\BaseRequest\BaseRequest;
class FeatureStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255|unique:features,key',
            'label' => 'required|string|max:255',
            'type' => 'required|in:boolean,number,text',
        ];
    }
}
