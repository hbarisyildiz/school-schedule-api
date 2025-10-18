<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassRoomResource extends JsonResource
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
            'school_id' => $this->school_id,
            'name' => $this->name,
            'grade' => $this->grade,
            'branch' => $this->branch,
            'capacity' => $this->capacity,
            'current_students' => $this->current_students,
            'classroom' => $this->classroom,
            'teacher_id' => $this->class_teacher_id,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // İlişkiler
            'teacher' => $this->whenLoaded('teacher', function() {
                return [
                    'id' => $this->teacher->id,
                    'name' => $this->teacher->name,
                    'email' => $this->teacher->email,
                    'short_name' => $this->teacher->short_name,
                ];
            }),
            'school' => $this->whenLoaded('school', function() {
                return [
                    'id' => $this->school->id,
                    'name' => $this->school->name,
                ];
            }),
        ];
    }
}

