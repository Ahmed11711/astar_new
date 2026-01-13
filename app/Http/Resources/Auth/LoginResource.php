<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id'         => $this->id,
                'email'      => $this->email,
                'first_name' => $this->first_name,
                'last_name'  => $this->last_name,
            ],
            'profile' => [
                'id'                => $this->id,
                'role'              => $this->role ?? null,
                'student_type'      => $this->student_type ?? null,
                'educational_stage' => $this->educational_stage ?? null,
                'school'            => $this->school ?? null,
            ],
            'last_student_package' => $this->latestStudentPackage ? [
                'package_id'    => $this->latestStudentPackage->package_id,
                'price'         => $this->latestStudentPackage->price,
                'starts_at'     => $this->latestStudentPackage->starts_at,
                'ends_at'       => $this->latestStudentPackage->ends_at,
                'status'        => $this->latestStudentPackage->status,
                'type'          => $this->latestStudentPackage->type,
            ] : null,
            'tokens' => [
                'access'  => $this->access_token ?? null,
                'refresh' => $this->access_token ?? null,
            ],
        ];
    }
}
