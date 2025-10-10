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
        Schema::create('school_registration_requests', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('school_code')->unique();
            $table->string('email')->unique();
            $table->string('phone');
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('district_id')->constrained('districts');
            $table->text('address');
            $table->string('principal_name'); // Müdür adı
            $table->string('principal_phone');
            $table->string('principal_email');
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('verification_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_registration_requests');
    }
};
