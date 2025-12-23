<?php

namespace App\Http\Resources\Admin\FeaturePackage;

use Illuminate\Http\Resources\Json\JsonResource;

class FeaturePackageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'package_id' => $this->package_id,
            'package_title' => $this->package->name ?? "",
            'feature_id' => $this->feature_id,
            'feature_name' => $this->feature->key ?? "",
            'value' => $this->value,
            'lable' => $this->lable ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
