<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\ScheduleConflict;
use Illuminate\Support\Collection;

class ScheduleConflictService
{
    /**
     * Ders programında çakışma var mı kontrol et
     */
    public function checkConflicts(Schedule $schedule): Collection
    {
        $conflicts = collect();
        
        // 1. Öğretmen çakışması kontrolü
        $teacherConflict = $this->checkTeacherConflict($schedule);
        if ($teacherConflict) {
            $conflicts->push($teacherConflict);
        }
        
        // 2. Sınıf çakışması kontrolü
        $classConflict = $this->checkClassConflict($schedule);
        if ($classConflict) {
            $conflicts->push($classConflict);
        }
        
        // 3. Derslik çakışması kontrolü (eğer derslik belirtilmişse)
        if ($schedule->classroom) {
            $classroomConflict = $this->checkClassroomConflict($schedule);
            if ($classroomConflict) {
                $conflicts->push($classroomConflict);
            }
        }
        
        // 4. Zaman geçerliliği kontrolü
        $timeConflict = $this->checkTimeValidity($schedule);
        if ($timeConflict) {
            $conflicts->push($timeConflict);
        }
        
        return $conflicts;
    }
    
    /**
     * Öğretmen aynı zamanda başka yerde mi?
     */
    private function checkTeacherConflict(Schedule $schedule): ?array
    {
        $conflict = Schedule::where('school_id', $schedule->school_id)
            ->where('teacher_id', $schedule->teacher_id)
            ->where('day_of_week', $schedule->day_of_week)
            ->where('period', $schedule->period)
            ->where('is_active', true)
            ->when($schedule->id, function($query) use ($schedule) {
                $query->where('id', '!=', $schedule->id); // Kendisi hariç
            })
            ->first();
        
        if ($conflict) {
            return [
                'type' => 'teacher_busy',
                'severity' => 'error',
                'conflicting_schedule_id' => $conflict->id,
                'description' => "Öğretmen {$schedule->teacher->name} bu saatte {$conflict->class->name} sınıfında {$conflict->subject->name} dersi veriyor."
            ];
        }
        
        return null;
    }
    
    /**
     * Sınıf aynı zamanda başka dersde mi?
     */
    private function checkClassConflict(Schedule $schedule): ?array
    {
        $conflict = Schedule::where('school_id', $schedule->school_id)
            ->where('class_id', $schedule->class_id)
            ->where('day_of_week', $schedule->day_of_week)
            ->where('period', $schedule->period)
            ->where('is_active', true)
            ->when($schedule->id, function($query) use ($schedule) {
                $query->where('id', '!=', $schedule->id);
            })
            ->first();
        
        if ($conflict) {
            return [
                'type' => 'class_busy',
                'severity' => 'error',
                'conflicting_schedule_id' => $conflict->id,
                'description' => "{$schedule->class->name} sınıfı bu saatte {$conflict->subject->name} dersinde ({$conflict->teacher->name})."
            ];
        }
        
        return null;
    }
    
    /**
     * Derslik dolu mu?
     */
    private function checkClassroomConflict(Schedule $schedule): ?array
    {
        $conflict = Schedule::where('school_id', $schedule->school_id)
            ->where('classroom', $schedule->classroom)
            ->where('day_of_week', $schedule->day_of_week)
            ->where('period', $schedule->period)
            ->where('is_active', true)
            ->when($schedule->id, function($query) use ($schedule) {
                $query->where('id', '!=', $schedule->id);
            })
            ->first();
        
        if ($conflict) {
            return [
                'type' => 'classroom_busy',
                'severity' => 'warning', // Derslik çakışması warning (başka derslik bulunabilir)
                'conflicting_schedule_id' => $conflict->id,
                'description' => "Derslik {$schedule->classroom} bu saatte {$conflict->class->name} sınıfı tarafından kullanılıyor."
            ];
        }
        
        return null;
    }
    
    /**
     * Zaman geçerliliği
     */
    private function checkTimeValidity(Schedule $schedule): ?array
    {
        // start_time < end_time kontrolü
        if ($schedule->start_time >= $schedule->end_time) {
            return [
                'type' => 'invalid_time',
                'severity' => 'error',
                'conflicting_schedule_id' => null,
                'description' => "Başlangıç saati ({$schedule->start_time}) bitiş saatinden ({$schedule->end_time}) önce olmalı."
            ];
        }
        
        return null;
    }
    
    /**
     * Çakışmaları veritabanına kaydet
     */
    public function saveConflicts(Schedule $schedule, Collection $conflicts): void
    {
        foreach ($conflicts as $conflictData) {
            ScheduleConflict::create([
                'school_id' => $schedule->school_id,
                'schedule_id' => $schedule->id,
                'conflict_type' => $conflictData['type'],
                'conflicting_schedule_id' => $conflictData['conflicting_schedule_id'],
                'severity' => $conflictData['severity'],
                'description' => $conflictData['description']
            ]);
        }
    }
    
    /**
     * Çakışmaları otomatik çöz (mümkünse)
     */
    public function resolveConflicts(Schedule $schedule): bool
    {
        $conflicts = $this->checkConflicts($schedule);
        
        // Error severity varsa çözülemez
        if ($conflicts->where('severity', 'error')->isNotEmpty()) {
            return false;
        }
        
        // Warning'ler varsa ama error yoksa kabul edilebilir
        return true;
    }
}

