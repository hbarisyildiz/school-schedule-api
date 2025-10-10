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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'super_admin', 'school_admin', 'teacher', 'student', 'staff'
            $table->string('display_name'); // 'Süper Admin', 'Okul Yöneticisi', 'Öğretmen'
            $table->text('description')->nullable();
            $table->json('permissions'); // JSON array izinler
            $table->integer('level')->default(0); // Yetki seviyesi (0=en yüksek)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
