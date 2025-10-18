<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    use ApiResponseTrait;

    /**
     * Derslikleri listele
     */
    public function index()
    {
        try {
            $classrooms = Classroom::where('school_id', auth()->user()->school_id)
                ->orderBy('type')
                ->orderBy('name')
                ->get();

            return $this->successResponse($classrooms, 'Derslikler listelendi');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Yeni derslik oluştur
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'type' => 'required|in:classroom,laboratory,workshop,music_room,computer_lab,art_room',
                'capacity' => 'required|integer|min:1|max:500',
                'equipment' => 'nullable|array',
                'description' => 'nullable|string'
            ]);

            $classroom = Classroom::create([
                'school_id' => auth()->user()->school_id,
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'capacity' => $request->capacity,
                'current_occupancy' => 0,
                'equipment' => $request->equipment,
                'description' => $request->description,
                'is_active' => true
            ]);

            return $this->createdResponse($classroom, 'Derslik başarıyla oluşturuldu');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Derslik detayı
     */
    public function show(string $id)
    {
        try {
            $classroom = Classroom::where('school_id', auth()->user()->school_id)
                ->findOrFail($id);

            return $this->successResponse($classroom, 'Derslik detayı');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Derslik güncelle
     */
    public function update(Request $request, string $id)
    {
        try {
            $classroom = Classroom::where('school_id', auth()->user()->school_id)
                ->findOrFail($id);

            $request->validate([
                'name' => 'string|max:255',
                'code' => 'nullable|string|max:50',
                'type' => 'in:classroom,laboratory,workshop,music_room,computer_lab,art_room',
                'capacity' => 'integer|min:1|max:500',
                'equipment' => 'nullable|array',
                'description' => 'nullable|string'
            ]);

            $classroom->update($request->only([
                'name', 'code', 'type', 'capacity', 'equipment', 'description'
            ]));

            return $this->updatedResponse($classroom, 'Derslik başarıyla güncellendi');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Derslik sil
     */
    public function destroy(string $id)
    {
        try {
            $classroom = Classroom::where('school_id', auth()->user()->school_id)
                ->findOrFail($id);

            $classroom->delete();

            return $this->deletedResponse('Derslik başarıyla silindi');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Derslik durumunu değiştir
     */
    public function toggleStatus(string $id)
    {
        try {
            $classroom = Classroom::where('school_id', auth()->user()->school_id)
                ->findOrFail($id);

            $classroom->update(['is_active' => !$classroom->is_active]);

            $message = $classroom->is_active ? 'Derslik aktifleştirildi' : 'Derslik pasifleştirildi';

            return $this->successResponse($classroom, $message);

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}



