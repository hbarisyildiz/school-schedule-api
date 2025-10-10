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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Okul adı
            $table->string('slug')->unique(); // URL için
            $table->string('code')->unique(); // Okul kodu (12345)
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable(); // Logo dosya yolu
            $table->string('website')->nullable();
            
            // Abonelik bilgileri
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans');
            $table->date('subscription_starts_at');
            $table->date('subscription_ends_at');
            $table->enum('subscription_status', ['active', 'expired', 'suspended', 'cancelled'])->default('active');
            
            // Kullanım istatistikleri
            $table->integer('current_teachers')->default(0);
            $table->integer('current_students')->default(0);
            $table->integer('current_classes')->default(0);
            
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
