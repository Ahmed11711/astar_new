<?php

namespace App\Http\Resources\Admin\successStories;

use Illuminate\Http\Resources\Json\JsonResource;

class successStoriesResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'title' => $this->title,
            'img' => $this->img,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
