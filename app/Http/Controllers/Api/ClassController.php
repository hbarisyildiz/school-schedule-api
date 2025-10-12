<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Sınıfları listele
     */
    public function index(Request $request)
    {
        $query = ClassRoom::with(['school', 'classTeacher'])
            ->where('school_id', auth()->user()->school_id);

        // Aktif/pasif filtresi
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Sınıf seviyesi filtresi
        if ($request->grade) {
            $query->where('grade', $request->grade);
        }

        // Arama
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $classes = $query->orderBy('grade')->orderBy('branch')->paginate(15);

        return response()->json($classes);
    }

    /**
     * Yeni sınıf oluştur
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'grade' => 'required|string|max:10',
            'branch' => 'required|string|max:5',
            'class_teacher_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string'
        ]);

        $class = ClassRoom::create([
            'school_id' => auth()->user()->school_id,
            'name' => $request->name,
            'grade' => $request->grade,
            'branch' => $request->branch,
            'capacity' => 30, // Default kapasite
            'class_teacher_id' => $request->class_teacher_id,
            'description' => $request->description,
            'is_active' => true
        ]);
        
        $class->load(['classTeacher', 'school']);

        return response()->json([
            'message' => 'Sınıf başarıyla oluşturuldu',
            'class' => $class
        ], 201);
    }

    /**
     * Sınıf detayı
     */
    public function show(string $id)
    {
        $class = ClassRoom::with(['school', 'classTeacher', 'students', 'schedules'])
            ->where('school_id', auth()->user()->school_id)
            ->findOrFail($id);

        return response()->json([
            'class' => $class,
            'statistics' => [
                'total_students' => $class->current_students,
                'capacity' => $class->capacity,
                'fill_rate' => $class->capacity > 0 ? round(($class->current_students / $class->capacity) * 100, 1) : 0,
                'total_schedules' => $class->schedules()->count()
            ]
        ]);
    }

    /**
     * Sınıf güncelle
     */
    public function update(Request $request, string $id)
    {
        $class = ClassRoom::where('school_id', auth()->user()->school_id)
            ->findOrFail($id);

        $request->validate([
            'name' => 'string|max:255',
            'grade' => 'string|max:10',
            'branch' => 'string|max:5',
            'class_teacher_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string'
        ]);

        $class->update($request->only([
            'name', 'grade', 'branch', 'class_teacher_id', 'description'
        ]));

        return response()->json([
            'message' => 'Sınıf başarıyla güncellendi',
            'class' => $class->fresh()->load(['classTeacher', 'school'])
        ]);
    }

    /**
     * Sınıf sil
     */
    public function destroy(string $id)
    {
        $class = ClassRoom::where('school_id', auth()->user()->school_id)
            ->findOrFail($id);

        // Sınıfa ait ders programları varsa uyar
        if ($class->schedules()->count() > 0) {
            return response()->json([
                'message' => 'Bu sınıfa ait ders programları var. Önce programları silin.',
                'schedule_count' => $class->schedules()->count()
            ], 422);
        }

        $class->delete();

        return response()->json([
            'message' => 'Sınıf başarıyla silindi'
        ]);
    }

    /**
     * Sınıf durumunu değiştir (aktif/pasif)
     */
    public function toggleStatus(string $id)
    {
        $class = ClassRoom::where('school_id', auth()->user()->school_id)
            ->findOrFail($id);

        $class->update(['is_active' => !$class->is_active]);

        return response()->json([
            'message' => $class->is_active ? 'Sınıf aktifleştirildi' : 'Sınıf pasifleştirildi',
            'class' => $class
        ]);
    }

    /**
     * Sınıf öğretmenlerini listele
     */
    public function getTeachers()
    {
        $teachers = User::where('school_id', auth()->user()->school_id)
            ->whereHas('role', function($query) {
                $query->where('name', 'teacher');
            })
            ->where('is_active', true)
            ->get(['id', 'name', 'email']);

        return response()->json($teachers);
    }
}
