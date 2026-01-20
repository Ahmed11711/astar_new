<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'username' => $this->username,
            'email'    => $this->email,

            'grades' => $this->whenLoaded('grades', function () {
                return $this->grades->map(fn($grade) => [
                    'id'   => $grade->id,
                    'name' => $grade->name,
                ]);
            }),

            'subjects' => $this->whenLoaded('subjects', function () {
                return $this->subjects->map(fn($subject) => [
                    'id'   => $subject->id,
                    'name' => $subject->name,
                ]);
            }),
        ];
    }
}
