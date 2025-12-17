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
                'role'               => $this->role ?? null,
                'student_type'       => $this->student_type ?? null,
                'educational_stage'  => $this->educational_stage ?? null,
                'phone'              => $this->phone ?? null,
                'locale'             => $this->locale ?? null,
                'school_id'          => $this->school_id ?? null,
            ],

            'subscription' => [
                'plan'        => $this->subscription?->plan ?? null,
                'expires_at'  => optional($this->subscription?->expires_at)
                    ->format('Y-m-d') ?? null,
            ],
        ];
    }
}
