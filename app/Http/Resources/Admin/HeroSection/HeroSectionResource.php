<?php

namespace App\Http\Resources\Admin\HeroSection;

use Illuminate\Http\Resources\Json\JsonResource;

class HeroSectionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'background_image' => $this->background_image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
