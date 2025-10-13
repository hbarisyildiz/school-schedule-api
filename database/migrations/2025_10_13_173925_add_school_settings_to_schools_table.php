<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Ders günleri (JSON format: ["monday", "tuesday", "wednesday", "thursday", "friday"])
            $table->json('class_days')->nullable()->after('is_active');
            
            // Ders süresi (dakika)
            $table->integer('lesson_duration')->default(40)->after('class_days');
            
            // Tenefüs süreleri (JSON format: {"small_break": 10, "lunch_break": 20})
            $table->json('break_durations')->nullable()->after('lesson_duration');
            
            // Ders saatleri (JSON format: {"start_time": "08:00", "end_time": "16:00"})
            $table->json('school_hours')->nullable()->after('break_durations');
            
            // Haftalık ders sayısı
            $table->integer('weekly_lesson_count')->default(30)->after('school_hours');
            
            // Ders programı ayarları
            $table->json('schedule_settings')->nullable()->after('weekly_lesson_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'class_days',
                'lesson_duration',
                'break_durations',
                'school_hours',
                'weekly_lesson_count',
                'schedule_settings'
            ]);
        });
    }
};
