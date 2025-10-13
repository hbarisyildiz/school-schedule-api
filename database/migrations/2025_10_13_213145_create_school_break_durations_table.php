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
        Schema::create('school_break_durations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->integer('after_period')->comment('Hangi periyottan sonra (1, 2, 3, ...)');
            $table->integer('duration')->comment('Süre (dakika)');
            $table->boolean('is_lunch_break')->default(false)->comment('Öğle arası mı?');
            $table->timestamps();
            
            // Unique constraint: Bir okulda aynı periyottan sonra birden fazla tenefüs olamaz
            $table->unique(['school_id', 'after_period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_break_durations');
    }
};
