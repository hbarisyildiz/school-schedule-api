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
        Schema::create('schedule_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Değişiklik bilgileri
            $table->enum('action', ['created', 'updated', 'deleted', 'restored'])->comment('Yapılan işlem');
            $table->json('old_data')->nullable()->comment('Eski veri (JSON)');
            $table->json('new_data')->nullable()->comment('Yeni veri (JSON)');
            $table->text('reason')->nullable()->comment('Değişiklik sebebi');
            
            // İzleme bilgileri
            $table->string('ip_address', 45)->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            // İndeksler
            $table->index(['school_id', 'created_at'], 'idx_school_date');
            $table->index(['schedule_id', 'action'], 'idx_schedule_action');
            $table->index(['user_id', 'created_at'], 'idx_user_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_change_logs');
    }
};
