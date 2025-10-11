<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Kritik performans indeksleri - Sık kullanılan sorgular için
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // En çok sorgulanacak kombinasyonlar
            $table->index(['school_id', 'day_of_week', 'period'], 'idx_schedules_school_day_period');
            $table->index(['teacher_id', 'day_of_week', 'start_time'], 'idx_schedules_teacher_time');
            $table->index(['class_id', 'is_active'], 'idx_schedules_class_active');
            $table->index(['school_id', 'is_active', 'start_date'], 'idx_schedules_active');
        });

        Schema::table('users', function (Blueprint $table) {
            // Kullanıcı sorgulamaları
            $table->index(['school_id', 'role_id', 'is_active'], 'idx_users_school_role');
            $table->index(['email', 'is_active'], 'idx_users_email_active');
            $table->index(['school_id', 'is_active'], 'idx_users_school_active');
        });

        Schema::table('classes', function (Blueprint $table) {
            // Sınıf sorgulamaları
            $table->index(['school_id', 'grade', 'is_active'], 'idx_classes_school_grade');
            $table->index(['school_id', 'is_active'], 'idx_classes_school_active');
            $table->index(['class_teacher_id'], 'idx_classes_teacher');
        });

        Schema::table('subjects', function (Blueprint $table) {
            // Ders sorgulamaları
            $table->index(['school_id', 'is_active'], 'idx_subjects_school_active');
            $table->index(['code'], 'idx_subjects_code');
        });

        Schema::table('schools', function (Blueprint $table) {
            // Okul sorgulamaları
            $table->index(['subscription_status', 'is_active'], 'idx_schools_subscription');
            $table->index(['subscription_ends_at'], 'idx_schools_expiry');
            $table->index(['city_id', 'district_id'], 'idx_schools_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex('idx_schedules_school_day_period');
            $table->dropIndex('idx_schedules_teacher_time');
            $table->dropIndex('idx_schedules_class_active');
            $table->dropIndex('idx_schedules_active');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_school_role');
            $table->dropIndex('idx_users_email_active');
            $table->dropIndex('idx_users_school_active');
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->dropIndex('idx_classes_school_grade');
            $table->dropIndex('idx_classes_school_active');
            $table->dropIndex('idx_classes_teacher');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropIndex('idx_subjects_school_active');
            $table->dropIndex('idx_subjects_code');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropIndex('idx_schools_subscription');
            $table->dropIndex('idx_schools_expiry');
            $table->dropIndex('idx_schools_location');
        });
    }
};
