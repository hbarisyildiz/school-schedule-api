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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            
            // Zaman bilgileri
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->integer('period'); // Ders saati (1, 2, 3, 4, 5, 6, 7, 8)
            $table->time('start_time'); // 08:00
            $table->time('end_time');   // 08:45
            
            // Ek bilgiler
            $table->string('classroom')->nullable(); // Derslik
            $table->text('notes')->nullable();
            $table->date('start_date'); // Bu programa başlangıç tarihi
            $table->date('end_date')->nullable(); // Bitiş tarihi (null = sürekli)
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Aynı okul, sınıf, gün, ders saatinde tek program olmalı
            $table->unique(['school_id', 'class_id', 'day_of_week', 'period', 'start_date'], 'unique_schedule_slot');
            
            // Aynı öğretmen aynı zamanda başka yerde olamaz
            $table->index(['school_id', 'teacher_id', 'day_of_week', 'period', 'start_date'], 'teacher_conflict_check');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
