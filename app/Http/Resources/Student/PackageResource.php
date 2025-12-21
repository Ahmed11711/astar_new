<?php

namespace App\Http\Resources\Student;

use App\Http\Resources\Admin\FeaturePackage\FeaturePackageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'assignable_id' => $this->assignable_id,
            'assign_type' => $this->assign_type,
            'is_user_package' => $this->is_user_package ?? null,
            'features_package' => FeaturePackageResource::collection($this->featuresPackage),
        ];
    }
}
