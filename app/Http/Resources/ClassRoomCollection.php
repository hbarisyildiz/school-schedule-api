<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ClassRoomCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($class) {
                return [
                    'id' => $class->id,
                    'school_id' => $class->school_id,
                    'name' => $class->name,
                    'grade' => $class->grade,
                    'branch' => $class->branch,
                    'capacity' => $class->capacity,
                    'current_students' => $class->current_students,
                    'classroom' => $class->classroom,
                    'teacher_id' => $class->class_teacher_id,
                    'description' => $class->description,
                    'is_active' => $class->is_active,
                    'created_at' => $class->created_at,
                    'updated_at' => $class->updated_at,
                    'teacher' => $class->teacher ? [
                        'id' => $class->teacher->id,
                        'name' => $class->teacher->name,
                        'email' => $class->teacher->email,
                        'short_name' => $class->teacher->short_name,
                    ] : null,
                    'school' => $class->school ? [
                        'id' => $class->school->id,
                        'name' => $class->school->name,
                    ] : null,
                ];
            }),
            'links' => $this->resource->links(),
            'meta' => $this->resource->meta(),
        ];
    }
}
