<?php

namespace App\Http\Resources\Admin\Subtopic;

use Illuminate\Http\Resources\Json\JsonResource;

class SubtopicResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'topic_id' => $this->topic_id,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
