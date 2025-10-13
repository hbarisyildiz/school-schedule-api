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
            // Günlük ders sayıları (JSON format: {"monday": 8, "tuesday": 10, ...})
            $table->json('daily_lesson_counts')->nullable()->after('schedule_settings');
            
            // Sınıf bazlı günlük ders sayıları (JSON format: {"9-A": {"monday": 8, "tuesday": 10}, ...})
            $table->json('class_daily_lesson_counts')->nullable()->after('daily_lesson_counts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'daily_lesson_counts',
                'class_daily_lesson_counts'
            ]);
        });
    }
};
