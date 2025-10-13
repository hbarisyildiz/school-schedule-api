<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\ClassRoom;
use App\Models\User;
use App\Models\ClassDailySchedule;
use App\Models\TeacherDailySchedule;

class MigrateScheduleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📦 Mevcut JSON verilerini yeni tablolara taşıyorum...');
        
        // Tüm okulları al
        $schools = School::all();
        
        foreach ($schools as $school) {
            $this->command->info("  📚 {$school->name} okulu işleniyor...");
            
            // 1. Sınıf verilerini taşı
            $this->migrateClassData($school);
            
            // 2. Öğretmen verilerini taşı
            $this->migrateTeacherData($school);
        }
        
        $this->command->info('✅ Veri taşıma işlemi tamamlandı!');
    }
    
    /**
     * Sınıf verilerini taşı
     */
    private function migrateClassData(School $school)
    {
        $classDailyLessonCounts = $school->class_daily_lesson_counts ?? [];
        
        if (empty($classDailyLessonCounts)) {
            $this->command->warn("    ⚠️  Sınıf verisi yok, atlanıyor...");
            return;
        }
        
        foreach ($classDailyLessonCounts as $className => $days) {
            // Sınıfı bul
            $class = ClassRoom::where('school_id', $school->id)
                ->where('name', $className)
                ->first();
            
            if (!$class) {
                $this->command->warn("    ⚠️  '{$className}' sınıfı bulunamadı, atlanıyor...");
                continue;
            }
            
            // Her gün için kayıt oluştur
            foreach ($days as $day => $lessonCount) {
                ClassDailySchedule::updateOrCreate(
                    [
                        'class_id' => $class->id,
                        'day' => $day
                    ],
                    [
                        'lesson_count' => $lessonCount
                    ]
                );
            }
            
            $this->command->info("    ✅ '{$className}' sınıfı taşındı");
        }
    }
    
    /**
     * Öğretmen verilerini taşı
     */
    private function migrateTeacherData(School $school)
    {
        $teacherDailyLessonCounts = $school->teacher_daily_lesson_counts ?? [];
        
        if (empty($teacherDailyLessonCounts)) {
            $this->command->warn("    ⚠️  Öğretmen verisi yok, atlanıyor...");
            return;
        }
        
        foreach ($teacherDailyLessonCounts as $teacherId => $days) {
            // Öğretmeni bul
            $teacher = User::where('id', $teacherId)
                ->where('school_id', $school->id)
                ->first();
            
            if (!$teacher) {
                $this->command->warn("    ⚠️  ID: {$teacherId} öğretmeni bulunamadı, atlanıyor...");
                continue;
            }
            
            // Her gün için kayıt oluştur
            foreach ($days as $day => $lessonCount) {
                TeacherDailySchedule::updateOrCreate(
                    [
                        'teacher_id' => $teacher->id,
                        'day' => $day
                    ],
                    [
                        'lesson_count' => $lessonCount
                    ]
                );
            }
            
            $this->command->info("    ✅ '{$teacher->name}' öğretmeni taşındı");
        }
    }
}
