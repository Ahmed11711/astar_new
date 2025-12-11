<?php

namespace App\Http\Requests\Admin\FeaturePackage;
use App\Http\Requests\BaseRequest\BaseRequest;
class FeaturePackageStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'package_id' => 'required|integer|exists:packages,id',
            'feature_id' => 'required|integer|exists:features,id',
            'value' => 'nullable|string|max:255',
        ];
    }
}
