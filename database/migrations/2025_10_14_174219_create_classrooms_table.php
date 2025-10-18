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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('name'); // Derslik adı (örn: 1-A Dersliği, Fizik Laboratuvarı)
            $table->string('code')->nullable(); // Derslik kodu (örn: A101, LAB-1)
            $table->string('type')->default('classroom'); // classroom, laboratory, workshop, music_room, computer_lab, art_room
            $table->integer('capacity')->default(30); // Kapasite
            $table->integer('current_occupancy')->default(0); // Mevcut doluluk
            $table->text('equipment')->nullable(); // Ekipman bilgisi (JSON)
            $table->text('description')->nullable(); // Açıklama
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('school_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
