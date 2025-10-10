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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('name'); // Matematik, Türkçe, İngilizce
            $table->string('code')->nullable(); // MAT, TUR, ING
            $table->string('color', 7)->default('#3498db'); // Hex renk kodu
            $table->text('description')->nullable();
            $table->integer('weekly_hours')->default(1); // Haftalık ders saati
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['school_id', 'code']); // Okul içinde ders kodu unique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
