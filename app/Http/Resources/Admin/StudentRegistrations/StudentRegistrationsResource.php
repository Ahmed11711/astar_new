<?php

namespace App\Http\Resources\Admin\StudentRegistrations;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentRegistrationsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'affiliation_type' => $this->affiliation_type,
            'user_id' => $this->user_id,
            'name' => $this->user->username ?? null,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
