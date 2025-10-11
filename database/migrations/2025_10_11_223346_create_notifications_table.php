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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Bildirim tipi ve içeriği
            $table->enum('type', ['schedule_change', 'announcement', 'reminder', 'alert', 'system'])->default('announcement');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable()->comment('Ek bilgiler (JSON)');
            
            // Durum
            $table->timestamp('read_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // İndeksler - Performans için
            $table->index(['user_id', 'read_at'], 'idx_user_unread');
            $table->index(['school_id', 'type', 'created_at'], 'idx_school_type');
            $table->index(['user_id', 'created_at'], 'idx_user_recent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
