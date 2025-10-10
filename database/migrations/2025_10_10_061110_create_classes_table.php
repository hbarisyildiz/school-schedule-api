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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('name'); // 9-A, 10-B, 11-C
            $table->string('grade'); // 9, 10, 11, 12
            $table->string('branch')->nullable(); // A, B, C, D
            $table->integer('capacity')->default(30); // Sınıf mevcudu
            $table->integer('current_students')->default(0);
            $table->string('classroom')->nullable(); // Derslik numarası
            $table->foreignId('class_teacher_id')->nullable()->constrained('users')->onDelete('set null'); // Sınıf öğretmeni
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['school_id', 'name']); // Okul içinde sınıf adı unique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
