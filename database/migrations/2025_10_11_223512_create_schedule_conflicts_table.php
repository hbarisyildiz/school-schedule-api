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
        Schema::create('schedule_conflicts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            
            // Çakışma bilgileri
            $table->enum('conflict_type', [
                'teacher_busy',      // Öğretmen başka yerde
                'classroom_busy',    // Derslik dolu
                'class_busy',        // Sınıf başka dersde
                'invalid_time'       // Geçersiz saat
            ])->comment('Çakışma tipi');
            
            $table->foreignId('conflicting_schedule_id')->nullable()->constrained('schedules')->onDelete('set null');
            
            $table->enum('severity', ['error', 'warning', 'info'])->default('warning');
            $table->text('description')->nullable();
            
            // Çözüm bilgileri
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamp('created_at')->useCurrent();
            
            // İndeksler
            $table->index(['school_id', 'resolved_at'], 'idx_school_unresolved');
            $table->index(['schedule_id', 'severity'], 'idx_schedule_severity');
            $table->index(['conflict_type', 'resolved_at'], 'idx_type_resolved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_conflicts');
    }
};
