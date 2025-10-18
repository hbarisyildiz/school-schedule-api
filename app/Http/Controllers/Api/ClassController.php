<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassRoomResource;
use App\Http\Resources\ClassRoomCollection;
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
        // Hızlı versiyon - öğretmen bilgisi ile
        try {
            $user = auth()->user();
            $role = $user->role->name;
            
            $query = ClassRoom::select('id', 'name', 'grade', 'branch', 'class_teacher_id')
                ->where('is_active', true);
            
            // Super admin değilse sadece kendi okulunun sınıflarını göster
            if ($role !== 'super_admin') {
                $query->where('school_id', $user->school_id);
            }
            
            $classes = $query->orderByRaw('CAST(grade AS UNSIGNED)')
                ->orderBy('branch')
                ->get();
            
            // Tüm öğretmen ID'lerini topla
            $teacherIds = $classes->pluck('class_teacher_id')->filter()->unique();
            
            // Tüm öğretmenleri tek seferde çek
            $teachers = \App\Models\User::select('id', 'name', 'email', 'short_name', 'branch')
                ->whereIn('id', $teacherIds)
                ->get()
                ->keyBy('id');
            
            // Her sınıf için öğretmen bilgisini ekle
            $result = $classes->map(function($class) use ($teachers) {
                $teacher = null;
                if ($class->class_teacher_id && isset($teachers[$class->class_teacher_id])) {
                    $teacherData = $teachers[$class->class_teacher_id];
                    $teacher = [
                        'id' => $teacherData->id,
                        'name' => $teacherData->name,
                        'email' => $teacherData->email,
                        'short_name' => $teacherData->short_name,
                        'branch' => $teacherData->branch,
                    ];
                }
                
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'grade' => $class->grade,
                    'branch' => $class->branch,
                    'class_teacher_id' => $class->class_teacher_id,
                    'teacher' => $teacher
                ];
            });
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Yeni sınıf oluştur
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'grade' => 'required',
            'branch' => 'required|string|max:5',
            'class_teacher_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'create_area' => 'nullable|boolean'
        ]);

        try {
            $class = ClassRoom::create([
                'school_id' => auth()->user()->school_id,
                'name' => $request->name,
                'grade' => $request->grade,
                'branch' => $request->branch,
                'class_teacher_id' => $request->class_teacher_id,
                'description' => $request->description,
                'is_active' => true
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Benzersizlik kısıtlaması hatası
            if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'classes_school_id_name_unique')) {
                return response()->json([
                    'message' => 'Bu sınıf adı zaten mevcut. Lütfen farklı bir sınıf adı seçin.',
                    'error' => 'duplicate_class_name'
                ], 422);
            }
            throw $e;
        }
        
        // Eğer create_area true ise, otomatik derslik oluştur
        if ($request->create_area) {
            \App\Models\Area::create([
                'school_id' => auth()->user()->school_id,
                'name' => $class->name . ' Dersliği',
                'code' => $class->name,
                'type' => 'classroom',
                'current_occupancy' => 0,
                'is_active' => true
            ]);
        }
        
        $class->load(['teacher', 'school']);

        return response()->json([
            'message' => 'Sınıf başarıyla oluşturuldu',
            'class' => new ClassRoomResource($class)
        ], 201);
    }

    /**
     * Sınıf detayı
     */
    public function show(string $id)
    {
        $class = ClassRoom::with(['school', 'teacher'])
            ->where('school_id', auth()->user()->school_id)
            ->findOrFail($id);

        return response()->json($class);
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
            'grade' => 'required',
            'branch' => 'string|max:5',
            'class_teacher_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'create_area' => 'nullable|boolean'
        ]);

        try {
            $class->update($request->only([
                'name', 'grade', 'branch', 'class_teacher_id', 'description'
            ]));
            
            // Sınıf ismi değiştiyse, ilgili dersliğin ismini de güncelle
            if ($request->has('name') && $request->name !== $class->getOriginal('name')) {
                \App\Models\Area::where('school_id', auth()->user()->school_id)
                    ->where('name', $class->getOriginal('name') . ' Dersliği')
                    ->update(['name' => $request->name . ' Dersliği']);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Benzersizlik kısıtlaması hatası
            if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'classes_school_id_name_unique')) {
                return response()->json([
                    'message' => 'Bu sınıf adı zaten mevcut. Lütfen farklı bir sınıf adı seçin.',
                    'error' => 'duplicate_class_name'
                ], 422);
            }
            throw $e;
        }

        // Eğer create_classroom true ise ve derslik yoksa, otomatik derslik oluştur
        if ($request->create_classroom) {
            $existingClassroom = \App\Models\Classroom::where('school_id', auth()->user()->school_id)
                ->where('name', $class->name . ' Dersliği')
                ->first();
            
            if (!$existingClassroom) {
                \App\Models\Classroom::create([
                    'school_id' => auth()->user()->school_id,
                    'name' => $class->name . ' Dersliği',
                    'code' => $class->name,
                    'type' => 'classroom',
                    'current_occupancy' => 0,
                    'is_active' => true
                ]);
            }
        }

        return response()->json([
            'message' => 'Sınıf başarıyla güncellendi',
            'class' => new ClassRoomResource($class->fresh()->load(['teacher', 'school']))
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
