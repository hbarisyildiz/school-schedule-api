<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    use ApiResponseTrait;
    /**
     * Ders programlarını listele
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['subject', 'teacher', 'classroom'])
            ->where('school_id', auth()->user()->school_id);

        // Sınıf filtresi
        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Öğretmen filtresi
        if ($request->teacher_id) {
            $query->where('teacher_id', $request->teacher_id);
        }

        // Ders filtresi
        if ($request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        // Günlük program
        if ($request->date) {
            $date = Carbon::parse($request->date);
            $query->where('day_of_week', $date->dayOfWeekIso);
        }

        // Haftalık program
        if ($request->week) {
            // Belirli hafta için program
        }

        $schedules = $query->orderBy('day_of_week')
                          ->orderBy('start_time')
                          ->paginate(50);

        return response()->json($schedules);
    }

    /**
     * Yeni ders programı oluştur
     */
    public function store(StoreScheduleRequest $request)
    {
        try {
            // Çakışma kontrolü
            $conflicts = $this->checkConflicts($request->validated());
            if (!empty($conflicts)) {
                return $this->errorResponse(
                    'Ders programında çakışma var',
                    422,
                    ['conflicts' => $conflicts]
                );
            }

            $schedule = Schedule::create([
                'school_id' => auth()->user()->school_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->teacher_id,
                'class_id' => $request->class_id,
                'classroom_id' => $request->classroom_id,
                'day_of_week' => $request->day_of_week,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'is_active' => true,
                'created_by' => auth()->id()
            ]);

            return $this->createdResponse(
                $schedule->load(['subject', 'teacher', 'classroom']),
                'Ders programı başarıyla oluşturuldu'
            );

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Ders programı detayı
     */
    public function show(string $id)
    {
        $schedule = Schedule::with(['subject', 'teacher', 'classroom', 'class'])
            ->where('school_id', auth()->user()->school_id)
            ->findOrFail($id);

        return response()->json($schedule);
    }

    /**
     * Ders programını güncelle
     */
    public function update(Request $request, string $id)
    {
        $schedule = Schedule::where('school_id', auth()->user()->school_id)
            ->findOrFail($id);

        $request->validate([
            'subject_id' => 'exists:subjects,id',
            'teacher_id' => 'exists:users,id',
            'class_id' => 'exists:classes,id',
            'classroom_id' => 'exists:classes,id',
            'day_of_week' => 'integer|between:1,7',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
            'semester' => 'in:1,2',
            'academic_year' => 'string',
        ]);

        // Çakışma kontrolü (kendisi hariç)
        $conflicts = $this->checkConflicts($request->all(), $schedule->id);
        if (!empty($conflicts)) {
            return response()->json([
                'message' => 'Ders programında çakışma var',
                'conflicts' => $conflicts
            ], 422);
        }

        $schedule->update($request->only([
            'subject_id', 'teacher_id', 'class_id', 'classroom_id',
            'day_of_week', 'start_time', 'end_time', 'semester', 'academic_year'
        ]));

        return response()->json([
            'message' => 'Ders programı güncellendi',
            'schedule' => $schedule->fresh()->load(['subject', 'teacher', 'classroom'])
        ]);
    }

    /**
     * Ders programını sil
     */
    public function destroy(string $id)
    {
        $schedule = Schedule::where('school_id', auth()->user()->school_id)
            ->findOrFail($id);

        $schedule->delete();

        return response()->json([
            'message' => 'Ders programı silindi'
        ]);
    }

    /**
     * Haftalık ders programı görünümü
     */
    public function weeklyView(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        
        $query = Schedule::with(['subject', 'teacher', 'classroom', 'class'])
            ->where('school_id', $schoolId)
            ->where('is_active', true);

        // Filtreleme
        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->teacher_id) {
            $query->where('teacher_id', $request->teacher_id);
        }

        $schedules = $query->get();

        // Haftalık program formatında düzenle
        $weeklySchedule = [];
        $days = ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'];
        
        for ($day = 1; $day <= 7; $day++) {
            $daySchedules = $schedules->where('day_of_week', $day)
                                   ->sortBy('start_time')
                                   ->values();
            
            $weeklySchedule[$days[$day-1]] = $daySchedules;
        }

        return response()->json([
            'weekly_schedule' => $weeklySchedule,
            'total_hours' => $schedules->count(),
            'filters' => [
                'class_id' => $request->class_id,
                'teacher_id' => $request->teacher_id
            ]
        ]);
    }

    /**
     * Öğretmen ders programı
     */
    public function teacherSchedule(Request $request, $teacherId = null)
    {
        $teacherId = $teacherId ?: auth()->id();
        $schoolId = auth()->user()->school_id;

        $schedules = Schedule::with(['subject', 'classroom', 'class'])
            ->where('school_id', $schoolId)
            ->where('teacher_id', $teacherId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'teacher_schedule' => $schedules,
            'total_hours' => $schedules->count(),
            'teacher' => User::find($teacherId)->only(['id', 'name', 'email'])
        ]);
    }

    /**
     * Sınıf ders programı
     */
    public function classSchedule($classId)
    {
        $schoolId = auth()->user()->school_id;

        $schedules = Schedule::with(['subject', 'teacher', 'classroom'])
            ->where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'class_schedule' => $schedules,
            'total_hours' => $schedules->count(),
            'class' => ClassRoom::find($classId)->only(['id', 'name', 'grade', 'capacity'])
        ]);
    }

    /**
     * Otomatik program oluştur
     */
    public function generateSchedule(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_year' => 'required|string',
            'semester' => 'required|in:1,2'
        ]);

        try {
            DB::beginTransaction();

            $generatedSchedules = $this->autoGenerateSchedule(
                $request->class_id,
                $request->academic_year,
                $request->semester
            );

            DB::commit();

            return response()->json([
                'message' => 'Otomatik program oluşturuldu',
                'generated_count' => count($generatedSchedules),
                'schedules' => $generatedSchedules
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Program oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Çakışma kontrolü
     */
    private function checkConflicts($data, $excludeId = null)
    {
        $conflicts = [];
        $schoolId = auth()->user()->school_id;

        // Öğretmen çakışması
        $teacherConflict = Schedule::where('school_id', $schoolId)
            ->where('teacher_id', $data['teacher_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where(function($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                      ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                      ->orWhere(function($q) use ($data) {
                          $q->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                      });
            })
            ->when($excludeId, function($query, $excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->first();

        if ($teacherConflict) {
            $conflicts[] = [
                'type' => 'teacher',
                'message' => 'Öğretmen bu saatte başka derste',
                'conflict_with' => $teacherConflict->id
            ];
        }

        // Sınıf çakışması
        $classConflict = Schedule::where('school_id', $schoolId)
            ->where('class_id', $data['class_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where(function($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                      ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                      ->orWhere(function($q) use ($data) {
                          $q->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                      });
            })
            ->when($excludeId, function($query, $excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->first();

        if ($classConflict) {
            $conflicts[] = [
                'type' => 'class',
                'message' => 'Sınıfın bu saatte başka dersi var',
                'conflict_with' => $classConflict->id
            ];
        }

        // Sınıf çakışması
        if (isset($data['classroom_id'])) {
            $classroomConflict = Schedule::where('school_id', $schoolId)
                ->where('classroom_id', $data['classroom_id'])
                ->where('day_of_week', $data['day_of_week'])
                ->where(function($query) use ($data) {
                    $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                          ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                          ->orWhere(function($q) use ($data) {
                              $q->where('start_time', '<=', $data['start_time'])
                                ->where('end_time', '>=', $data['end_time']);
                          });
                })
                ->when($excludeId, function($query, $excludeId) {
                    $query->where('id', '!=', $excludeId);
                })
                ->first();

            if ($classroomConflict) {
                $conflicts[] = [
                    'type' => 'classroom',
                    'message' => 'Bu sınıf bu saatte dolu',
                    'conflict_with' => $classroomConflict->id
                ];
            }
        }

        return $conflicts;
    }

    /**
     * Otomatik program oluşturma algoritması
     */
    private function autoGenerateSchedule($classId, $academicYear, $semester)
    {
        $schoolId = auth()->user()->school_id;
        
        // Mevcut programları temizle
        Schedule::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->delete();

        // Sınıfın derslerini al
        $subjects = Subject::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();

        // Öğretmenleri al
        $teachers = User::where('school_id', $schoolId)
            ->whereHas('role', function($query) {
                $query->where('name', 'teacher');
            })
            ->where('is_active', true)
            ->get();

        // Boş sınıfları al
        $classrooms = ClassRoom::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();

        $generatedSchedules = [];
        $timeSlots = [
            ['08:00', '08:45'],
            ['08:50', '09:35'], 
            ['09:40', '10:25'],
            ['10:45', '11:30'],
            ['11:35', '12:20'],
            ['13:00', '13:45'],
            ['13:50', '14:35'],
            ['14:40', '15:25']
        ];

        // Basit otomatik program oluşturma
        $dayIndex = 1; // Pazartesi'den başla
        $slotIndex = 0;

        foreach ($subjects as $subject) {
            $teacher = $teachers->random(); // Rastgele öğretmen ata
            $classroom = $classrooms->random(); // Rastgele sınıf ata
            
            $schedule = Schedule::create([
                'school_id' => $schoolId,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'class_id' => $classId,
                'classroom_id' => $classroom->id,
                'day_of_week' => $dayIndex,
                'start_time' => $timeSlots[$slotIndex][0],
                'end_time' => $timeSlots[$slotIndex][1],
                'semester' => $semester,
                'academic_year' => $academicYear,
                'is_active' => true,
                'created_by' => auth()->id()
            ]);

            $generatedSchedules[] = $schedule->load(['subject', 'teacher', 'classroom']);

            // Sonraki slot'a geç
            $slotIndex++;
            if ($slotIndex >= count($timeSlots)) {
                $slotIndex = 0;
                $dayIndex++;
                if ($dayIndex > 5) { // Sadece hafta içi
                    break;
                }
            }
        }

        return $generatedSchedules;
    }
}
