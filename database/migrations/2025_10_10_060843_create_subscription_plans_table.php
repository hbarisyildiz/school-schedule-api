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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'Temel', 'Standart', 'Premium'
            $table->string('slug')->unique(); // 'basic', 'standard', 'premium'
            $table->text('description')->nullable();
            $table->decimal('monthly_price', 8, 2);
            $table->decimal('yearly_price', 8, 2);
            $table->integer('max_teachers')->nullable(); // null = sınırsız
            $table->integer('max_students')->nullable(); // null = sınırsız
            $table->integer('max_classes')->nullable(); // null = sınırsız
            $table->json('features'); // JSON array özellikler
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
