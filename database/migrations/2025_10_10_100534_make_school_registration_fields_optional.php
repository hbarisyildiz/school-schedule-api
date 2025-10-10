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
        Schema::table('school_registration_requests', function (Blueprint $table) {
            // Zorunlu olmayan alanları nullable yap
            $table->string('school_code')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('principal_name')->nullable()->change();
            $table->string('principal_phone')->nullable()->change();
            $table->string('principal_email')->nullable()->change();
            $table->foreignId('subscription_plan_id')->nullable()->change();
        });

        // Schools tablosunda sadece code'u nullable yap, diğerleri zaten nullable
        Schema::table('schools', function (Blueprint $table) {
            $table->string('code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_registration_requests', function (Blueprint $table) {
            // Geri al
            $table->string('school_code')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->string('principal_name')->nullable(false)->change();
            $table->string('principal_phone')->nullable(false)->change();
            $table->string('principal_email')->nullable(false)->change();
            $table->foreignId('subscription_plan_id')->nullable(false)->change();
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->string('code')->nullable(false)->change();
        });
    }
};
