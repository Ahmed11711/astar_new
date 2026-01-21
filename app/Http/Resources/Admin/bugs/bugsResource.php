<?php

namespace App\Http\Resources\Admin\bugs;

use Illuminate\Http\Resources\Json\JsonResource;

class bugsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'question' => $this->question,
            'answer' => $this->answer,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
