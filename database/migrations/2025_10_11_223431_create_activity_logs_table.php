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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Aktivite bilgileri
            $table->string('action', 100)->comment('login, create_schedule, delete_user, etc');
            $table->string('entity_type', 50)->nullable()->comment('schedule, user, class, etc');
            $table->unsignedBigInteger('entity_id')->nullable();
            
            // Detaylar
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            // İndeksler - Log sorgulama için
            $table->index(['school_id', 'created_at'], 'idx_school_date');
            $table->index(['user_id', 'action'], 'idx_user_action');
            $table->index(['entity_type', 'entity_id'], 'idx_entity');
            $table->index(['action', 'created_at'], 'idx_action_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
