<?php

namespace App\Http\Requests\Student\Package;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class PackageUpgradeRequest extends BaseRequest
{


    public function rules(): array
    {
        return [
            'package_id' => 'required|exists:packages,id',
        ];
    }
}
