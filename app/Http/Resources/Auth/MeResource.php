<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'is_active'  => (bool) $this->is_active,

            'profile' => [
                'role'               => $this->profile?->role,
                'student_type'       => $this->profile?->student_type,
                'educational_stage'  => $this->profile?->educational_stage,
                'phone'              => $this->profile?->phone,
                'locale'             => $this->profile?->locale,
                'school_id'          => $this->profile?->school_id,
            ],

            'subscription' => [
                'plan'        => $this->subscription?->plan ?? null,
                'expires_at'  => optional($this->subscription?->expires_at)
                    ->format('Y-m-d') ?? null,
            ],
        ];
    }
}
