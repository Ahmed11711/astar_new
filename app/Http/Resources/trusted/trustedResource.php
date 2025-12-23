<?php

namespace App\Http\Resources\trusted;

use App\Http\Resources\Admin\FeaturePackage\FeaturePackageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class trustedResource extends JsonResource
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
            'title' => $this->title,
            'count' => $this->count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
